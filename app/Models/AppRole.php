<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Role for the mobile app's admin panel, with a configurable set of permissions.
 * Users point at it through users.app_role_id. Portal admins (Admin#<bid>) always have every permission.
 */
class AppRole extends Model
{
    protected $table = 'app_roles';

    protected $fillable = ['business_id', 'name', 'description', 'permissions', 'is_system'];

    protected $casts = ['permissions' => 'array', 'is_system' => 'boolean'];

    /** key => [group, label, help text]. "manage" implies "view" for the same module. */
    const PERMISSIONS = [
        'dashboard.view' => ['Dashboard', 'Business dashboard', 'All order, booking, roadside and maintenance figures for the business.'],
        'orders.view' => ['Orders', 'See orders', 'Order list and order details.'],
        'orders.manage' => ['Orders', 'Update orders', 'Confirm, complete, deny or cancel orders.'],
        'bookings.view' => ['Bookings', 'See bookings', 'Booking list and booking details.'],
        'bookings.manage' => ['Bookings', 'Update bookings', 'Accept, complete, deny or cancel bookings and change prices.'],
        'maintenance.view' => ['Car maintenance', 'See service records', 'Service history, vehicles, receipts and next-service cards.'],
        'maintenance.manage' => ['Car maintenance', 'Record services', 'Add, edit and delete service records and vehicles.'],
        'roadside.accept' => ['Roadside', 'Take roadside jobs', 'Hears the alarm for new requests and can accept them.'],
        'roadside.view_all' => ['Roadside', 'See all roadside requests', 'Every request, including jobs taken by other mechanics. Without it, only their own jobs.'],
        'roadside.manage' => ['Roadside', 'Supervise roadside jobs', 'Decline requests and update or cancel jobs assigned to someone else.'],
        'billboard.manage' => ['Billboard', 'Manage billboard', 'Post, reorder, hide and delete the slides on the customer app home screen.'],
    ];

    /** Roles created for every business that uses the app panel. */
    const DEFAULTS = [
        'App Admin' => [
            'description' => 'Everything in the app admin panel.',
            'permissions' => [
                'dashboard.view', 'orders.view', 'orders.manage', 'bookings.view', 'bookings.manage',
                'maintenance.view', 'maintenance.manage', 'roadside.accept', 'roadside.view_all',
                'roadside.manage', 'billboard.manage',
            ],
        ],
        'Mechanic' => [
            'description' => 'Roadside jobs: accepts new requests and sees only the jobs they took.',
            'permissions' => ['roadside.accept'],
        ],
    ];

    public static function allPermissions(): array
    {
        return array_keys(self::PERMISSIONS);
    }

    /** Permissions grouped for the portal form: group => [key => [label, help]]. */
    public static function groupedPermissions(): array
    {
        $groups = [];
        foreach (self::PERMISSIONS as $key => [$group, $label, $help]) {
            $groups[$group][$key] = [$label, $help];
        }

        return $groups;
    }

    /** Only known keys; a "manage" permission brings its "view" permission along. */
    public static function normalize(array $permissions): array
    {
        $valid = array_values(array_intersect(self::allPermissions(), $permissions));
        foreach (['orders', 'bookings', 'maintenance'] as $module) {
            if (in_array("$module.manage", $valid) && ! in_array("$module.view", $valid)) {
                $valid[] = "$module.view";
            }
        }

        return array_values(array_intersect(self::allPermissions(), $valid)); // keep catalogue order
    }

    /** Make sure the default roles exist for a business. */
    public static function ensureDefaults($businessId): void
    {
        foreach (self::DEFAULTS as $name => $default) {
            self::firstOrCreate(
                ['business_id' => $businessId, 'name' => $name],
                ['description' => $default['description'], 'permissions' => $default['permissions'], 'is_system' => true]
            );
        }
    }

    public function users()
    {
        return $this->hasMany(\App\User::class, 'app_role_id');
    }
}
