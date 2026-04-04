<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Make column permissive to normalize values, then convert to desired ENUM
        DB::statement("ALTER TABLE `attendance` MODIFY `checkout_status` VARCHAR(50) NOT NULL DEFAULT 'missing'");

        // Normalize common variants to the new target values
        DB::statement("UPDATE `attendance` SET `checkout_status` = 'completed' WHERE LOWER(`checkout_status`) = 'complete'");
        DB::statement("UPDATE `attendance` SET `checkout_status` = 'missing' WHERE LOWER(`checkout_status`) = 'missing'");

        // Finally convert to ENUM restricted to the desired values
        DB::statement("ALTER TABLE `attendance` MODIFY `checkout_status` ENUM('missing','completed') NOT NULL DEFAULT 'missing'");
    }

    public function down(): void
    {
        // Revert: make it permissive text again, then set legacy value 'complete'
        DB::statement("ALTER TABLE `attendance` MODIFY `checkout_status` VARCHAR(50) NOT NULL DEFAULT 'missing'");
        DB::statement("UPDATE `attendance` SET `checkout_status` = 'complete' WHERE `checkout_status` = 'completed'");
        DB::statement("ALTER TABLE `attendance` MODIFY `checkout_status` ENUM('missing','complete') NOT NULL DEFAULT 'missing'");
    }
};
