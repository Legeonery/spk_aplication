<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vehicles', function ($table) {
            $table->double('max_weight')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function ($table) {
            $table->double('max_weight')->nullable(false)->change();
        });
    }
};
