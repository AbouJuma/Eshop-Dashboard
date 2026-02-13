<?php


use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'users';

    /**
     * Run the migrations.
     * @table users
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->string('fname')->nullable();
            $table->string('mname')->nullable();
            $table->string('sname')->nullable();
            $table->string('username', 100);
            $table->string('email')->nullable();
            $table ->string( 'fcm_token' )->nullable( ); 
            $table->string('phone', 45);
            $table->unsignedInteger('role_id')->default(1);
            $table->timestamp('email_verified_at')->nullable()->default(null);
            $table->string('password')->nullable();
            $table->string('image')->nullable();
            $table->rememberToken();
            $table->nullableTimestamps();

            $table->unique(["email"], 'users_email_unique');
            $table->unique(["phone"], 'phone_UNIQUE');

            $table->index(["role_id"], 'fk_users_roles_idx');

            $table->foreign('role_id', 'fk_users_roles_idx')
                ->references('id')->on('roles')
                ->onDelete('no action')
                ->onUpdate('no action');
                
        });

        DB::table("users")->insert([
            "fname" => "Admin",
            "mname" => "User",
            "sname" => "Admin",
            "username" => "alphaAdmin",
            "email" => "admin@admin.com",
            'phone' => '0756808677',
            "password" => bcrypt("Garage@2023"),
        ]);

        DB::table("users")->insert([
            "fname" => "Client",
            "mname" => "First",
            "sname" => "User",
            "username" => "alphaClient",
            "email" => "client@garage.com",
            'phone' => '0700000000',
            'role_id' => 2,
            "password" => bcrypt("0700000000"),
        ]);
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
