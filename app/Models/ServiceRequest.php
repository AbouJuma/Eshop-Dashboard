<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * On-demand roadside / mobile mechanic request.
 */
class ServiceRequest extends Model
{
    protected $table = 'service_requests';

    protected $guarded = ['id'];

    protected $casts = [
        'customer_lat' => 'float',
        'customer_lng' => 'float',
        'mechanic_lat' => 'float',
        'mechanic_lng' => 'float',
        'price' => 'float',
        'mechanic_location_at' => 'datetime',
        'accepted_at' => 'datetime',
        'started_at' => 'datetime',
        'arrived_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    const SERVICE_TYPES = [
        'breakdown' => 'Breakdown',
        'battery' => 'Battery / Jump start',
        'tyre' => 'Flat tyre',
        'towing' => 'Towing',
        'fuel' => 'Out of fuel',
        'lockout' => 'Locked out',
        'maintenance' => 'Maintenance at my place',
        'other' => 'Other',
    ];

    /** Statuses in which the request is still running. */
    const ACTIVE_STATUSES = ['pending', 'accepted', 'on_the_way', 'arrived', 'in_progress'];

    /** Allowed admin transitions: current status => next statuses. */
    const TRANSITIONS = [
        'accepted' => ['on_the_way'],
        'on_the_way' => ['arrived'],
        'arrived' => ['in_progress', 'completed'],
        'in_progress' => ['completed'],
    ];

    const CUSTOMER_MESSAGES = [
        'accepted' => ['Mechanic assigned', 'A mechanic has accepted your request :ref.'],
        'on_the_way' => ['Mechanic on the way', 'Your mechanic is heading to you. Track them live in the app.'],
        'arrived' => ['Mechanic arrived', 'Your mechanic has arrived at your location.'],
        'in_progress' => ['Work started', 'Your mechanic has started working on your vehicle.'],
        'completed' => ['Service completed', 'Your request :ref has been completed. Thank you for using Dadis!'],
        'cancelled' => ['Request cancelled', 'Your request :ref has been cancelled.'],
        'rejected' => ['Request declined', 'Sorry, we cannot attend to your request :ref right now.'],
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mechanic()
    {
        return $this->belongsTo(\App\User::class, 'assigned_to');
    }

    public function isActive(): bool
    {
        return in_array($this->status, self::ACTIVE_STATUSES);
    }

    public static function generateReference(): string
    {
        do {
            $ref = 'RSR'.now()->format('ymd').strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
        } while (self::where('reference', $ref)->exists());

        return $ref;
    }

    /** Payload shared by the customer and admin APIs. */
    public function toApiArray(): array
    {
        $mechanic = $this->mechanic;

        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'service_type' => $this->service_type,
            'service_label' => self::SERVICE_TYPES[$this->service_type] ?? ucfirst($this->service_type),
            'description' => $this->description,
            'vehicle' => $this->vehicle,
            'contact_phone' => $this->contact_phone ?: optional($this->client)->phone,
            'customer_name' => optional($this->client)->username ?? 'Customer',
            'customer_lat' => $this->customer_lat,
            'customer_lng' => $this->customer_lng,
            'address' => $this->address,
            'status' => $this->status,
            'mechanic' => $mechanic ? [
                'name' => trim($mechanic->first_name.' '.$mechanic->last_name) ?: $mechanic->username,
                'phone' => $mechanic->contact_no ?: $mechanic->phone,
            ] : null,
            'mechanic_lat' => $this->mechanic_lat,
            'mechanic_lng' => $this->mechanic_lng,
            'mechanic_location_at' => optional($this->mechanic_location_at)->toIso8601String(),
            'price' => $this->price,
            'cancellation_reason' => $this->cancellation_reason,
            'cancelled_by' => $this->cancelled_by,
            'created_at' => optional($this->created_at)->format('Y-m-d H:i'),
            'accepted_at' => optional($this->accepted_at)->format('Y-m-d H:i'),
            'arrived_at' => optional($this->arrived_at)->format('Y-m-d H:i'),
            'completed_at' => optional($this->completed_at)->format('Y-m-d H:i'),
        ];
    }
}
