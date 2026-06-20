<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Business;

class CarMaintenance extends Model
{
    protected $table = 'car_maintenances';

    protected $fillable = [
        'business_id',
        'car_id',
        'service_date',
        'serviced_kilometer',
        'next_service_kilometer',
        'service_type',
        'details',
        'cost'
    ];

    protected $casts = [
        'service_date' => 'date',
        'cost' => 'decimal:2',
        'serviced_kilometer' => 'integer',
        'next_service_kilometer' => 'integer'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }
}
