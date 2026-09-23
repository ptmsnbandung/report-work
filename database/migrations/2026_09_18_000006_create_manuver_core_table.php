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
        Schema::create('manuver_core', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tiket')->constrained('tiket')->onDelete('cascade');
            $table->string('titik', 50);
            $table->string('core_asal', 50);
            $table->string('core_tujuan', 50);
            $table->enum('tipe', ['SEBELUM', 'SESUDAH']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manuver_core');
    }
};
