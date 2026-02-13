<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingSubService extends Model
{
    use HasFactory;

    protected $table = 'booking_sub_services';

    protected $fillable = [
        'sub_service_id',
        'booking_id',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'id');
    }

    public function subService()
    {
        return $this->belongsTo(SubService::class, 'sub_service_id', 'id');
    }
}
