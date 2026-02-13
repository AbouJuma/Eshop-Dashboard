<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderEshopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_eshops', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order_id');
            $table->string('p_name')->nullable();
            $table->string('p_image')->nullable();
            $table->string('p_price')->nullable();
            $table->string('p_quantity')->nullable();
            $table->string('p_sku')->nullable();
            $table->timestamps();

            $table->index(["order_id"], 'fk_order_eshop_orders1_idx');

            $table->foreign('order_id', 'fk_order_eshop_orders1_idx')
                ->references('id')->on('orders')
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
        Schema::dropIfExists('order_eshops');
    }
}
