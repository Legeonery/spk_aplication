<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('drivers', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->after('id');
            $table->string('categories')->nullable()->after('license_number');
        });
    }

    public function down(): void {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'categories']);
        });
    }
};
