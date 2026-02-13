<?php


use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'orders';

    /**
     * Run the migrations.
     * @table orders
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->enum('status', ['pending', 'cancelled', 'confirmed'])->default('pending')->nullable();
            $table->decimal('amount', 13, 2)->nullable();
            $table->uuid('reference_no')->nullable();
            $table->unsignedBigInteger('user_id');

            $table->index(["user_id"], 'fk_orders_users1_idx');
            $table->nullableTimestamps();


            $table->foreign('user_id', 'fk_orders_users1_idx')
                ->references('id')->on('users')
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
