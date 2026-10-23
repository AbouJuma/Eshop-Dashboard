<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Chat between the customer and the mechanic (mobile admin) on a roadside request.
 * Kept permanently for reference.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::create('service_request_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_request_id');
            $table->string('sender_type', 10);           // customer | admin
            $table->unsignedBigInteger('sender_id');
            $table->text('body');
            $table->timestamp('read_at')->nullable();    // when the other side saw it
            $table->timestamps();

            $table->index(['service_request_id', 'id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_request_messages');
    }
};
