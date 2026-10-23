<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The app's service cart filters prices by vehicle (make, model, year) and service;
 * without indexes every request scanned the whole table.
 */
return new class extends Migration
{
    private $indexes = [
        'svp_vehicle_lookup_index' => ['make_id', 'model_id', 'year_from', 'year_to'],
        'svp_sub_service_index' => ['sub_service_id'],
        'svp_model_index' => ['model_id'],
    ];

    public function up()
    {
        foreach ($this->indexes as $name => $columns) {
            if (! $this->hasIndex($name)) {
                Schema::table('service_vehicle_prices', fn (Blueprint $table) => $table->index($columns, $name));
            }
        }
    }

    public function down()
    {
        foreach (array_keys($this->indexes) as $name) {
            if ($this->hasIndex($name)) {
                Schema::table('service_vehicle_prices', fn (Blueprint $table) => $table->dropIndex($name));
            }
        }
    }

    private function hasIndex(string $name): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'service_vehicle_prices')
            ->where('index_name', $name)
            ->exists();
    }
};
