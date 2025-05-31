<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouse_grains', function (Blueprint $table) {
            // Удаляем старое поле и добавляем новое с нужным типом
            $table->dropColumn('amount');
        });

        Schema::table('warehouse_grains', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->default(0)->after('grain_type_id');

            // Уникальность по складу и типу культуры
            $table->unique(['warehouse_id', 'grain_type_id'], 'warehouse_grain_unique');
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_grains', function (Blueprint $table) {
            $table->dropUnique('warehouse_grain_unique');
            $table->dropColumn('amount');
        });

        Schema::table('warehouse_grains', function (Blueprint $table) {
            $table->integer('amount')->after('grain_type_id');
        });
    }
};
