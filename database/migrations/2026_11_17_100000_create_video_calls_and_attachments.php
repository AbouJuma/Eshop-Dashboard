<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * - service_request_calls: free in-app video calls (WebRTC) between customer and mechanic on a roadside job.
 *   The server only relays the call setup (offer/answer); audio and video go phone to phone.
 * - media_attachments: reference photos / videos on car service records and roadside jobs.
 */
return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('service_request_calls')) {
            Schema::create('service_request_calls', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('service_request_id')->index();
                $table->string('caller_type', 20); // customer | admin
                $table->unsignedBigInteger('caller_id');
                $table->unsignedBigInteger('answered_by')->nullable();
                $table->string('status', 20)->default('ringing'); // ringing | active | ended | declined | missed
                $table->longText('offer');
                $table->longText('answer')->nullable();
                $table->timestamp('answered_at')->nullable();
                $table->timestamp('ended_at')->nullable();
                $table->string('ended_by', 20)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('media_attachments')) {
            Schema::create('media_attachments', function (Blueprint $table) {
                $table->id();
                $table->string('attachable_type', 60);
                $table->unsignedBigInteger('attachable_id');
                $table->string('file_path');
                $table->string('media_type', 10); // image | video
                $table->string('mime', 100)->nullable();
                $table->unsignedBigInteger('size')->default(0);
                $table->string('caption')->nullable();
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->string('uploader_type', 20)->default('staff'); // staff | customer
                $table->timestamps();
                $table->index(['attachable_type', 'attachable_id']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('service_request_calls');
        Schema::dropIfExists('media_attachments');
    }
};
