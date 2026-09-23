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
        Schema::create('notifikasi_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tiket')->constrained('tiket')->onDelete('cascade');
            $table->enum('tipe', ['WA', 'EMAIL', 'INAPP'])->index();
            $table->string('penerima', 100);
            $table->text('pesan');
            $table->enum('status', ['SENT', 'FAILED'])->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi_log');
    }
};
