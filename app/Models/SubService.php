<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubService extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'service_id',
        'description',
        'image',
        'discount',
        'status',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }


    //booking services
    public function bookingSubServices()
    {
        return $this->hasMany(BookingSubService::class, 'sub_service_id', 'id');
    }

    //service vehicle prices
    public function serviceVehiclePrices()
    {
        return $this->hasMany(ServiceVehiclePrice::class, 'sub_service_id', 'id');
    }

    //delete sub service and all related data in boot method
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($subService) {
            $subService->bookingSubServices()->delete();
            if ($subService->image != null) {
                $image_path = public_path() . '/images/sub_services/' . $subService->image;
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
        });
    }

   
    
}
