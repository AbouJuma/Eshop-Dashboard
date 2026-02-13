<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleMfgYearsTable extends Migration
{
    public $tableName = 'vehicle_mfg_years';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('model_id');
            $table->string('year');
            $table->nullableTimestamps();

            $table->index(["model_id"], 'fk_mfg_years_models1_idx');

            $table->foreign('model_id', 'fk_mfg_years_models1_idx')
                ->references('id')->on('models')
                ->onDelete('cascade')
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
