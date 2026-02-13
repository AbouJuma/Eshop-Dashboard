<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueServiceVehiclePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_vehicle_prices', function(Blueprint $table){
            $table->unique(['sub_service_id', 'make_id', 'model_id', 'year_from', 'year_to'], 'unique_service_vehicle_prices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_vehicle_prices', function (Blueprint $table) {
            $table->dropUnique('unique_service_vehicle_prices');
        });
    }
}
