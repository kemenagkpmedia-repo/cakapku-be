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
        Schema::create('iksks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_sasaran_kegiatan');
            $table->text('indikator')->nullable();
            $table->timestamps();

            $table->foreign('id_sasaran_kegiatan')->references('id')->on('sasaran_kegiatans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iksks');
    }
};
