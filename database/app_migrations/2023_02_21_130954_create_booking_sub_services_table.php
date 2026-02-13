<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingSubServicesTable extends Migration
{
    public $tableName = 'booking_sub_services';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('sub_service_id');
            $table->unsignedInteger('booking_id');

            $table->index(["sub_service_id"], 'fk_booking_service_sub_service1_idx');

            $table->index(["booking_id"], 'fk_booking_service_booking1_idx');

            $table->nullableTimestamps();

            $table->foreign('sub_service_id', 'fk_booking_service_sub_service1_idx')
                ->references('id')->on('sub_services')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('booking_id', 'fk_booking_service_booking1_idx')
                ->references('id')->on('booking')
                ->onDelete('no action')
                ->onUpdate('no action');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
}
