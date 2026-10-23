<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class CreateMobileAdmin extends Command
{
    protected $signature = 'dadis:create-mobile-admin
                            {username : Login username}
                            {password : Login password (min 8 characters)}
                            {--business=1 : Business ID the admin belongs to}
                            {--name=Mobile Admin : Display name}';

    protected $description = 'Create (or reset) an admin account that can log into the mobile app admin panel and the web portal';

    public function handle()
    {
        $businessId = (int) $this->option('business');
        $password = $this->argument('password');

        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters.');

            return 1;
        }

        $role = Role::where('name', 'Admin#'.$businessId)->where('business_id', $businessId)->first();
        if (! $role) {
            $this->error("Role Admin#{$businessId} not found. Check the --business option.");

            return 1;
        }

        $user = User::withTrashed()->firstOrNew(['username' => $this->argument('username')]);
        $isNew = ! $user->exists;

        $name = explode(' ', $this->option('name'), 2);
        // first_name, password, etc. are $guarded on App\User, so assign attributes directly.
        $user->user_type = 'user';
        $user->username = $this->argument('username');
        $user->first_name = $name[0];
        $user->last_name = $name[1] ?? null;
        $user->password = bcrypt($password);
        $user->language = $user->language ?: 'en';
        $user->business_id = $businessId;
        $user->allow_login = 1;
        $user->status = 'active';
        $user->deleted_at = null;
        $user->save();

        $user->syncRoles([$role]);

        $this->info(($isNew ? 'Created' : 'Updated')." admin '{$user->username}' (id {$user->id}) for business {$businessId}.");

        return 0;
    }
}
