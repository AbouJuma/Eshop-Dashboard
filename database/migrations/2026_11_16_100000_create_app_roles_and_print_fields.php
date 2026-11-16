<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * - app_roles: dynamic roles + permissions for the mobile admin panel (users.app_role_id)
 * - car_maintenances: new service categories, next service date, who recorded it
 * - schedules (billboard): manual display order, real show/hide flag
 */
return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('app_roles')) {
            Schema::create('app_roles', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('business_id');
                $table->string('name', 100);
                $table->string('description')->nullable();
                $table->text('permissions')->nullable(); // JSON array of permission keys
                $table->boolean('is_system')->default(false);
                $table->timestamps();
                $table->unique(['business_id', 'name']);
            });
        }

        if (! Schema::hasColumn('users', 'app_role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('app_role_id')->nullable()->index();
            });
        }

        // Existing app accounts (Spatie roles "Mobile Admin#<bid>" / "Mechanic#<bid>") move to app roles.
        $legacy = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', 'App\\User')
            ->where(fn ($q) => $q->where('roles.name', 'like', 'Mobile Admin#%')->orWhere('roles.name', 'like', 'Mechanic#%'))
            ->get(['model_has_roles.model_id as user_id', 'roles.name']);

        foreach ($legacy as $row) {
            [$type, $businessId] = explode('#', $row->name, 2);
            $roleId = $this->systemRole((int) $businessId, $type === 'Mechanic' ? 'Mechanic' : 'App Admin');
            DB::table('users')->where('id', $row->user_id)->whereNull('app_role_id')->update(['app_role_id' => $roleId]);
        }

        Schema::table('car_maintenances', function (Blueprint $table) {
            if (! Schema::hasColumn('car_maintenances', 'next_service_date')) {
                $table->date('next_service_date')->nullable()->after('next_service_kilometer');
            }
            if (! Schema::hasColumn('car_maintenances', 'created_by')) {
                $table->unsignedInteger('created_by')->nullable()->index();
            }
        });

        // Old categories -> Oil Change / Periodic Maintenance / Repair Work
        $map = [
            'normal' => 'oil_change', 'car_services' => 'oil_change',
            'both' => 'periodic_maintenance', 'periodic_maintanance' => 'periodic_maintenance',
            'spares' => 'repair',
        ];
        foreach ($map as $old => $new) {
            DB::table('car_maintenances')->where('service_type', $old)->update(['service_type' => $new]);
        }

        if (! Schema::hasColumn('schedules', 'sort_order')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->integer('sort_order')->default(0)->index();
            });

            // keep today's order (newest date first) and show every existing slide
            // (visibility used to be saved as the text "public", which became 0)
            $position = 0;
            foreach (DB::table('schedules')->orderByDesc('date')->orderByDesc('id')->pluck('id') as $id) {
                DB::table('schedules')->where('id', $id)->update(['sort_order' => $position++, 'visibility' => 1]);
            }
        }
    }

    private function systemRole(int $businessId, string $name): int
    {
        $existing = DB::table('app_roles')->where('business_id', $businessId)->where('name', $name)->value('id');
        if ($existing) {
            return $existing;
        }

        return DB::table('app_roles')->insertGetId([
            'business_id' => $businessId,
            'name' => $name,
            'description' => \App\Models\AppRole::DEFAULTS[$name]['description'],
            'permissions' => json_encode(\App\Models\AppRole::DEFAULTS[$name]['permissions']),
            'is_system' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::table('schedules', fn (Blueprint $table) => $table->dropColumn('sort_order'));
        Schema::table('car_maintenances', fn (Blueprint $table) => $table->dropColumn(['next_service_date', 'created_by']));
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('app_role_id'));
        Schema::dropIfExists('app_roles');
    }
};
