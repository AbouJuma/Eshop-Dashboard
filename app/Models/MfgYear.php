<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MfgYear extends Model
{
    protected $table = 'vehicle_mfg_years';
    use HasFactory;

    protected $fillable = [
        'model_id',
        'year'
    ];

    public function model()
    {
        return $this->belongsTo(VehicleModel::class);
    }
}
