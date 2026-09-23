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
        Schema::create('master_materials', function (Blueprint $table) {
            $table->id();
            $table->string('kode_material', 50)->unique();
            $table->string('nama_material', 150);
            $table->string('satuan', 50)->default('Pcs');
            $table->integer('stok_tersedia')->default(0);
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_materials');
    }
};
