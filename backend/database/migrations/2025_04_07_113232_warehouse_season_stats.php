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
        Schema::create('warehouse_season_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            
            $table->string('season'); // например, "2024", "2024/2025"
        
            // Итоговая загрузка за сезон
            $table->float('delivered_volume')->default(0); // всего привезено
            $table->float('shipped_volume')->default(0);   // всего отгружено
        
            // Расчётная максимальная загрузка за сезон
            $table->float('peak_volume')->default(0); // рассчитывается: delivered - shipped на каждом этапе
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_season_stats');
    }
};
