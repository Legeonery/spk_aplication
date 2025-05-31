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
        Schema::create('grain_logs', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type'); // delivery / shipment
            $table->unsignedBigInteger('entity_id');
            $table->json('changes'); // {"volume": ["старое", "новое"], ...}
            $table->string('action'); // updated
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grain_logs');
    }
};
