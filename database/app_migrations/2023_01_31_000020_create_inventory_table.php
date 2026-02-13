<?php


use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'inventory';

    /**
     * Run the migrations.
     * @table inventory
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('quantity')->nullable();
            $table->enum('status', ['OUT', 'LOW', 'NORM'])->nullable();
            $table->unsignedInteger('product_id');

            $table->index(["product_id"], 'fk_inventory_products1_idx');
            $table->nullableTimestamps();


            $table->foreign('product_id', 'fk_inventory_products1_idx')
                ->references('id')->on('products')
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
