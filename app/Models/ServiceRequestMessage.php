<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequestMessage extends Model
{
    protected $table = 'service_request_messages';

    protected $guarded = ['id'];

    protected $casts = ['read_at' => 'datetime'];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'sender_type' => $this->sender_type,
            'body' => $this->body,
            'read' => $this->read_at !== null,
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
