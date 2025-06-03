<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('grain_deliveries', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->foreign('driver_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('grain_deliveries', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->foreign('driver_id')->references('id')->on('drivers')->nullOnDelete();
        });
    }
};