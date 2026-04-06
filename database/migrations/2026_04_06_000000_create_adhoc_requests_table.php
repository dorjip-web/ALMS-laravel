<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('adhoc_requests')) {
            Schema::create('adhoc_requests', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('adhoc_id')->nullable()->index();
                $table->unsignedBigInteger('application_id')->nullable()->index();
                $table->unsignedBigInteger('employee_id')->nullable()->index();
                $table->string('eid')->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->date('date')->nullable();
                $table->string('purpose')->nullable();
                $table->string('remarks')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('adhoc_requests')) {
            Schema::dropIfExists('adhoc_requests');
        }
    }
};
