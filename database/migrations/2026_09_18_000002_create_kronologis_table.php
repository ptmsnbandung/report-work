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
        Schema::create('kronologis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tiket')->constrained('tiket')->onDelete('cascade');
            $table->dateTime('timestamp')->index();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('kategori', [
                'IZIN',
                'OTDR',
                'TRACING',
                'MATERIAL',
                'JOINTING',
                'LINK_UP',
                'SELESAI',
                'LAIN'
            ])->index();
            $table->text('informasi');
            $table->string('foto_url', 255)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kronologis');
    }
};
