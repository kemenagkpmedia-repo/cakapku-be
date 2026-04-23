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
        Schema::table('iksks', function (Blueprint $table) {
            $table->string('target_vol')->nullable()->after('indikator');
            $table->string('target_satuan')->nullable()->after('target_vol');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iksks', function (Blueprint $table) {
            $table->dropColumn(['target_vol', 'target_satuan']);
        });
    }
};
