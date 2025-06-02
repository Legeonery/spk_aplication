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
        DB::statement("ALTER TABLE vehicles MODIFY type ENUM('работа в поле', 'привоз', 'отгрузка', 'универсальный') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE vehicles MODIFY type ENUM('привоз', 'отгрузка', 'универсальный') NOT NULL");
    }
};
