<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVehicleKindIdToVehiclesTable extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_kind_id')->after('number')->nullable();
            $table->foreign('vehicle_kind_id')->references('id')->on('vehicle_kinds')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['vehicle_kind_id']);
            $table->dropColumn('vehicle_kind_id');
        });
    }
}
