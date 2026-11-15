<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Some tables were imported without AUTO_INCREMENT on `id` (same problem as the earlier
 * activity_log / notification fixes). Inserting a second row then fails with
 * "Duplicate entry '0' for key 'PRIMARY'" — e.g. creating the Mechanic role or a billboard.
 *
 * For each affected table: move a row stuck at id 0 to a real id (updating references),
 * then enable AUTO_INCREMENT. Tables that are already fine are left untouched.
 */
return new class extends Migration
{
    /** table => [table.column that reference its id] */
    private $tables = [
        'roles' => ['model_has_roles.role_id', 'role_has_permissions.role_id'],
        'permissions' => ['model_has_permissions.permission_id', 'role_has_permissions.permission_id'],
        'schedules' => [],
    ];

    public function up()
    {
        foreach ($this->tables as $table => $references) {
            $column = DB::selectOne(
                'SELECT COLUMN_TYPE, EXTRA FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                [$table, 'id']
            );
            if (! $column || stripos($column->EXTRA, 'auto_increment') !== false) {
                continue;
            }

            DB::transaction(function () use ($table, $references) {
                if (DB::table($table)->where('id', 0)->exists()) {
                    $newId = (int) DB::table($table)->max('id') + 1;
                    DB::table($table)->where('id', 0)->update(['id' => $newId]);
                    foreach ($references as $reference) {
                        [$refTable, $refColumn] = explode('.', $reference);
                        DB::table($refTable)->where($refColumn, 0)->update([$refColumn => $newId]);
                    }
                }
            });

            DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `id` {$column->COLUMN_TYPE} NOT NULL AUTO_INCREMENT");
        }

        // spatie/permission caches roles; make sure the renumbered ids are picked up
        app()['cache']->forget('spatie.permission.cache');
    }

    public function down()
    {
        // Intentionally not reversible: removing AUTO_INCREMENT would bring the bug back.
    }
};
