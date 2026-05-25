<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MakeAccountDeletionRequestsEmailNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `account_deletion_requests` MODIFY `email` VARCHAR(191) NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `account_deletion_requests` MODIFY `email` VARCHAR(191) NOT NULL");
    }
}
