<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceVehiclePrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_vehicle_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sub_service_id');
            $table->unsignedInteger('make_id');
            $table->unsignedInteger('model_id');
            $table->double('price');
            $table->double('discount');
            $table->double('year_from');
            $table->double('year_to');
            $table->timestamps();

            $table->foreign('sub_service_id')->references('id')->on('sub_services')->onDelete('cascade');
            $table->foreign('make_id')->references('id')->on('makes')->onDelete('cascade');
            $table->foreign('model_id')->references('id')->on('models')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_vehicle_prices');
    }
}
