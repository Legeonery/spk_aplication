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
        Schema::create('grain_deliveries', function (Blueprint $table) {
            $table->id();
        
            // Склад, на который доставляется зерно
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
        
            // Тип зерновой культуры
            $table->string('grain_type');
        
            // Объём в тоннах
            $table->float('volume');
        
            // Дата поступления (важно для сезонного анализа)
            $table->date('delivery_date');
        
            // Транспорт и водитель
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grain_deliveries');
    }
};
