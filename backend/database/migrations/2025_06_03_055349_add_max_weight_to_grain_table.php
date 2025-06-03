<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('grain_deliveries', function (Blueprint $table) {
            $table->unsignedInteger('max_weight')->nullable()->after('volume');
        });

        Schema::table('grain_shipments', function (Blueprint $table) {
            $table->unsignedInteger('max_weight')->nullable()->after('volume');
        });
    }

    public function down()
    {
        Schema::table('grain_deliveries', function (Blueprint $table) {
            $table->dropColumn('max_weight');
        });

        Schema::table('grain_shipments', function (Blueprint $table) {
            $table->dropColumn('max_weight');
        });
    }
};
