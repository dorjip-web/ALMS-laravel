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
        if (! Schema::hasTable('device_bindings')) {
            return;
        }

        Schema::table('device_bindings', function (Blueprint $table) {
            if (! Schema::hasColumn('device_bindings', 'session_id')) {
                $table->string('session_id')->nullable()->after('device_token');
            }
            if (! Schema::hasColumn('device_bindings', 'device_type')) {
                $table->string('device_type', 32)->nullable()->after('session_id');
            }
            if (! Schema::hasColumn('device_bindings', 'last_seen')) {
                $table->timestamp('last_seen')->nullable()->after('device_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('device_bindings')) {
            return;
        }

        Schema::table('device_bindings', function (Blueprint $table) {
            if (Schema::hasColumn('device_bindings', 'last_seen')) {
                $table->dropColumn('last_seen');
            }
            if (Schema::hasColumn('device_bindings', 'device_type')) {
                $table->dropColumn('device_type');
            }
            if (Schema::hasColumn('device_bindings', 'session_id')) {
                $table->dropColumn('session_id');
            }
        });
    }
};
