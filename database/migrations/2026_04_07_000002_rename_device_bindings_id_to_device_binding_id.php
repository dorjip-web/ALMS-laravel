<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('device_bindings') && Schema::hasColumn('device_bindings', 'id') && ! Schema::hasColumn('device_bindings', 'device_binding_id')) {
            // Rename primary key column `id` to `device_binding_id`.
            // Use raw statement to avoid requiring doctrine/dbal.
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE `device_bindings` CHANGE `id` `device_binding_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;');
            } elseif ($driver === 'sqlite') {
                // SQLite: recreate table is more complex; skip if sqlite
            } else {
                // Fallback: attempt generic rename (may fail)
                Schema::table('device_bindings', function ($table) {
                    $table->renameColumn('id', 'device_binding_id');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('device_bindings') && Schema::hasColumn('device_bindings', 'device_binding_id') && ! Schema::hasColumn('device_bindings', 'id')) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE `device_bindings` CHANGE `device_binding_id` `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;');
            } elseif ($driver === 'sqlite') {
                // skip
            } else {
                Schema::table('device_bindings', function ($table) {
                    $table->renameColumn('device_binding_id', 'id');
                });
            }
        }
    }
};
