<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Billboard slides (schedules) can be an image / animated GIF or a short video.
 */
return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('schedules', 'media_type')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->string('media_type', 10)->default('image')->after('image'); // image | video
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('schedules', 'media_type')) {
            Schema::table('schedules', fn (Blueprint $table) => $table->dropColumn('media_type'));
        }
    }
};
