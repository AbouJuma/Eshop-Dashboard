<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleModel extends Model
{
    protected $table = 'models';

    use HasFactory;

    protected $fillable = [
        'name',
        'make_id'
    ];

    public function make()
    {
        return $this->belongsTo(Make::class);
    }

    public function years()
    {
        $serviceVehiclePrices = ServiceVehiclePrice::where('model_id', $this->id)->get();
        $years = [];
        foreach ($serviceVehiclePrices as $serviceVehiclePrice) {
            $years[] = $serviceVehiclePrice->year_from;
            $years[] = $serviceVehiclePrice->year_to;
        }
        $years = array_unique($years);
        sort($years);
        if (count($years) < 2) {
            return [
                'min' => 0,
                'max' => 0,
            ];
        }else{
            return [
                'min' => $years[0],
                'max' => $years[count($years) - 1],
            ];
        }
    }
}
