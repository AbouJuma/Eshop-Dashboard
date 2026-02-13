<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceVehiclePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'sub_service_id',
        'make_id',
        'model_id',
        'year_from',
        'year_to',
        'price',
        'discount',
    ];

    public function subService()
    {
        return $this->belongsTo(SubService::class);
    }


    public function make()
    {
        return $this->belongsTo(Make::class);
    }

    public function model()
    {
        return $this->belongsTo(VehicleModel::class);
    }
}
