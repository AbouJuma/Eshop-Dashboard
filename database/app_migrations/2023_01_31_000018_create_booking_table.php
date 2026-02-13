<?php


use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'booking';

    /**
     * Run the migrations.
     * @table booking
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->date('date')->nullable();
            $table->time('from')->nullable();
            $table->time('to')->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->decimal('grand_total', 13, 2)->default('0')->nullable();
            $table->decimal('final_total', 13, 2)->default('0')->nullable();
            $table->enum('status', ['pending', 'cancelled', 'completed','accepted'])->default('pending')->nullable();
            $table->string('vehicle')->nullable();
            $table->unsignedBigInteger('user_id');
            
            $table->unsignedInteger('address_id');

            $table->index(["user_id"], 'fk_booking_users1_idx');


            $table->index(["address_id"], 'fk_booking_address1_idx');
            $table->nullableTimestamps();


            $table->foreign('user_id', 'fk_booking_users1_idx')
                ->references('id')->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('address_id', 'fk_booking_address1_idx')
                ->references('id')->on('address')
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
