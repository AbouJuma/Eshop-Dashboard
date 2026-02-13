<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('booking')->whereNull('status')->update(['status' => 'pending']);
        DB::statement("ALTER TABLE `booking` MODIFY COLUMN `status` ENUM('pending','cancelled','completed','accepted','denied') NULL DEFAULT 'pending'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE `booking` MODIFY COLUMN `status` ENUM('pending','cancelled','completed','accepted') NULL DEFAULT 'pending'");
    }
};
