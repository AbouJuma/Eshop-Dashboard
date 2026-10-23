<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * On-demand (roadside) service requests: a customer asks for a mechanic at their
 * location and follows the mechanic live on a map, Uber style.
 */
return new class extends Migration
{
    public function up()
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 30)->unique();
            $table->unsignedBigInteger('user_id')->index();          // customer
            $table->string('service_type', 50);                      // breakdown, battery, tyre, ...
            $table->text('description')->nullable();
            $table->string('vehicle')->nullable();
            $table->string('contact_phone', 30)->nullable();

            $table->decimal('customer_lat', 10, 7);
            $table->decimal('customer_lng', 10, 7);
            $table->string('address')->nullable();

            // pending, accepted, on_the_way, arrived, in_progress, completed, cancelled, rejected
            $table->string('status', 20)->default('pending')->index();
            $table->unsignedBigInteger('assigned_to')->nullable()->index(); // admin user who took the job

            $table->decimal('mechanic_lat', 10, 7)->nullable();
            $table->decimal('mechanic_lng', 10, 7)->nullable();
            $table->timestamp('mechanic_location_at')->nullable();

            $table->decimal('price', 22, 2)->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->string('cancelled_by', 20)->nullable();          // customer | admin

            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('started_at')->nullable();             // trip started
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_requests');
    }
};
