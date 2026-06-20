<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('unsatisfied_reason')->nullable()->after('status');
        });

        Schema::table('booking', function (Blueprint $table) {
            $table->text('unsatisfied_reason')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('unsatisfied_reason');
        });

        Schema::table('booking', function (Blueprint $table) {
            $table->dropColumn('unsatisfied_reason');
        });
    }
};
