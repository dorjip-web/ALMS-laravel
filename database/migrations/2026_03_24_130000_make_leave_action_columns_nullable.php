<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE leave_application MODIFY HoD_action_by INT NULL');
        DB::statement('ALTER TABLE leave_application MODIFY HoD_action_at TIMESTAMP NULL');
        DB::statement('ALTER TABLE leave_application MODIFY medical_superintendent_action_by INT NULL');
        DB::statement('ALTER TABLE leave_application MODIFY medical_superintendent_action_at TIMESTAMP NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE leave_application MODIFY HoD_action_by INT NOT NULL');
        DB::statement('ALTER TABLE leave_application MODIFY HoD_action_at TIMESTAMP NOT NULL');
        DB::statement('ALTER TABLE leave_application MODIFY medical_superintendent_action_by INT NOT NULL');
        DB::statement('ALTER TABLE leave_application MODIFY medical_superintendent_action_at TIMESTAMP NOT NULL');
    }
};