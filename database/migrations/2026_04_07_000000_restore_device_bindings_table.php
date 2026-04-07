<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('device_bindings')) {
            Schema::create('device_bindings', function (Blueprint $table) {
                $table->bigIncrements('id');
                // Use signed integer to match legacy `tab1.employee_id` type
                $table->integer('employee_id')->nullable()->index();
                $table->string('device_token', 191)->nullable();
                $table->timestamp('bind_date')->nullable();
                $table->timestamps();
            });

            // Add foreign key to tab1.employee_id when that table/column exists
            if (Schema::hasTable('tab1') && in_array('employee_id', Schema::getColumnListing('tab1'), true)) {
                Schema::table('device_bindings', function (Blueprint $table) {
                    $table->foreign('employee_id', 'fk_employee_device')
                        ->references('employee_id')
                        ->on('tab1')
                        ->onDelete('cascade');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('device_bindings')) {
            Schema::dropIfExists('device_bindings');
        }
    }
};
