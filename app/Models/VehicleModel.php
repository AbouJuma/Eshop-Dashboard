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
        if (array_key_exists('year_range', $this->attributes)) {
            return $this->attributes['year_range'];
        }

        $years = [];
        foreach (ServiceVehiclePrice::where('model_id', $this->id)->get(['year_from', 'year_to']) as $price) {
            $years[] = $price->year_from;
            $years[] = $price->year_to;
        }

        return self::rangeFromYears($years);
    }

    /**
     * Year ranges for every model in one query (avoids one query per model on /api/makes).
     *
     * @return array<int, array{min: mixed, max: mixed}> keyed by model id
     */
    public static function allYearRanges(): array
    {
        $yearsByModel = [];
        foreach (\DB::table('service_vehicle_prices')->select('model_id', 'year_from', 'year_to')->cursor() as $row) {
            $yearsByModel[$row->model_id][] = $row->year_from;
            $yearsByModel[$row->model_id][] = $row->year_to;
        }

        return array_map([self::class, 'rangeFromYears'], $yearsByModel);
    }

    private static function rangeFromYears(array $years): array
    {
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
