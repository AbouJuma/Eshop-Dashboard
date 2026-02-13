<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('orders')->whereNull('status')->update(['status' => 'pending']);

        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM('pending','cancelled','confirmed','completed','denied') NULL DEFAULT 'pending'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM('pending','cancelled','confirmed') NULL DEFAULT 'pending'");
    }
};
