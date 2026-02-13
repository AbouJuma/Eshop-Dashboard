<?php


use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'products';

    /**
     * Run the migrations.
     * @table products
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 45)->nullable();
            $table->tinyInteger('pin')->nullable();
            $table->string('image')->nullable();
            $table->text("description")->nullable();
            $table->unsignedInteger('product_category_id');

            $table->index(["product_category_id"], 'fk_products_product_categories1_idx');
            $table->nullableTimestamps();


            $table->foreign('product_category_id', 'fk_products_product_categories1_idx')
                ->references('id')->on('product_categories')
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
