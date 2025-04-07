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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            // Основная информация
            $table->string('number')->unique(); // номерной знак
            $table->enum('type', ['привоз', 'отгрузка', 'универсальный']);
            // Технические характеристики
            $table->float('max_weight'); // максимально допустимая масса
            $table->enum('status', ['на ходу', 'на ремонте', 'не на ходу'])->default('на ходу');
            // Ответственный водитель
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            // Дополнительно
            $table->boolean('is_available')->default(true); // доступность для назначения
            $table->text('notes')->nullable(); // заметки (например, описание поломки)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
