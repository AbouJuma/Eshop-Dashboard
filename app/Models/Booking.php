<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'booking';
    
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_id',
        'from',
        'to',
        'date',
        'status',
        'reference_number',
        'grand_total',
        'final_total',
        'vehicle'
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id', 'id');
    }

    public function services()
    {
        return $this->hasMany(BookingSubService::class);
    }

    //sub bookingSubServices
    public function bookingSubServices()
    {
        return $this->hasMany(BookingSubService::class, 'booking_id', 'id');
    }

    public function subServices()
    {
        return $this->hasManyThrough(SubService::class, BookingSubService::class, 'booking_id', 'id', 'id', 'sub_service_id');
    }

    


    //TODO::Revisit this logic
    public function getDateTimeAttribute()
    {
        $convertedDate = \DateTime::createFromFormat('Y-m-d', $date)->format('Y-m-d');
        return $convertedDate;
    }

    //delete booking services when booking is deleted 
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($booking) {
            $booking->services()->delete();
        });
    }
}
