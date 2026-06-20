<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Business;

class Car extends Model
{
    protected $table = 'cars';

    protected $fillable = [
        'business_id',
        'car_plate_number',
        'car_chassis_number',
        'car_brand',
        'car_model',
        'car_year',
        'owner_name',
        'owner_phone',
        'owner_email'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function maintenances()
    {
        return $this->hasMany(CarMaintenance::class, 'car_id');
    }
}
