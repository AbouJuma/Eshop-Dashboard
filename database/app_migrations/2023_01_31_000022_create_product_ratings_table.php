<?php


use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductRatingsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'product_ratings';

    /**
     * Run the migrations.
     * @table product_ratings
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('attempts')->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->unsignedInteger('product_id');

            $table->index(["product_id"], 'fk_product_ratings_products1_idx');
            $table->nullableTimestamps();


            $table->foreign('product_id', 'fk_product_ratings_products1_idx')
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
