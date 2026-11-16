<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * In-app video call on a roadside job. Only the WebRTC set-up (offer / answer) passes through the server;
 * the audio and video go directly between the two phones, so calls cost nothing beyond mobile data.
 */
class ServiceRequestCall extends Model
{
    protected $table = 'service_request_calls';

    protected $guarded = ['id'];

    protected $casts = [
        'answered_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /** Unanswered calls stop ringing after this many seconds. */
    const RING_SECONDS = 45;

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function caller()
    {
        return $this->belongsTo(\App\User::class, 'caller_id');
    }

    /** A ringing call nobody picked up becomes "missed" (checked whenever the call is read). */
    public function expireIfUnanswered(): self
    {
        if ($this->status === 'ringing' && $this->created_at && $this->created_at->lt(now()->subSeconds(self::RING_SECONDS))) {
            $this->update(['status' => 'missed', 'ended_at' => now()]);
        }

        return $this;
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['ringing', 'active']);
    }

    public function callerName(): string
    {
        $user = $this->caller;
        if (! $user) {
            return $this->caller_type === 'customer' ? 'Customer' : 'Mechanic';
        }

        return trim($user->first_name.' '.$user->last_name) ?: ($user->username ?: 'Dadis');
    }

    /**
     * STUN finds each phone's public address; TURN (optional, set in .env) relays the media when
     * two networks cannot connect directly.
     */
    public static function iceServers(): array
    {
        $servers = [['urls' => ['stun:stun.l.google.com:19302', 'stun:stun1.l.google.com:19302']]];

        $config = config('services.webrtc', []);
        $turn = array_filter(array_map('trim', explode(',', (string) ($config['turn_urls'] ?? ''))));
        if ($turn) {
            $servers[] = [
                'urls' => array_values($turn),
                'username' => (string) ($config['turn_username'] ?? ''),
                'credential' => (string) ($config['turn_credential'] ?? ''),
            ];
        }

        return $servers;
    }

    public function toApiArray(string $viewer): array
    {
        return [
            'id' => $this->id,
            'service_request_id' => $this->service_request_id,
            'status' => $this->status,
            'incoming' => $this->caller_type !== $viewer,
            'caller_type' => $this->caller_type,
            'caller_name' => $this->callerName(),
            // the side that has to read the other's description gets it
            'offer' => $this->caller_type !== $viewer ? json_decode($this->offer, true) : null,
            'answer' => $this->caller_type === $viewer && $this->answer ? json_decode($this->answer, true) : null,
            'ice_servers' => self::iceServers(),
            'ring_seconds' => self::RING_SECONDS,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'answered_at' => optional($this->answered_at)->toIso8601String(),
            'ended_at' => optional($this->ended_at)->toIso8601String(),
        ];
    }
}
