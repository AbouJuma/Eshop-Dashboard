<?php


use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubServicesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'sub_services';

    /**
     * Run the migrations.
     * @table sub_services
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 100)->nullable();
            $table->unsignedInteger('service_id');
            $table->text('description')->nullable();
            $table->integer('discount')->nullable();
            $table->string('image')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->unique(["title", "service_id"], 'title_service_unique');

            $table->nullableTimestamps();

            $table->index(["service_id"], 'fk_sub_services_services_idx');


            $table->foreign('service_id', 'fk_sub_services_services_idx')
                ->references('id')->on('services')
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
