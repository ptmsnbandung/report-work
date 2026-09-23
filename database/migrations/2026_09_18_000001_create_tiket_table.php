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
        Schema::create('tiket', function (Blueprint $table) {
            $table->id();
            $table->string('no_tiket', 20)->unique();
            $table->string('status_link_impact', 150);
            $table->string('backbone_segment', 100)->index();
            $table->text('deskripsi')->nullable();
            $table->dateTime('tanggal_open')->index();
            $table->dateTime('tanggal_close')->nullable()->index();
            $table->enum('status', ['OPEN', 'PROSES', 'CLOSE'])->default('OPEN')->index();
            $table->integer('mttr_minutes')->nullable();
            $table->integer('sla_target_minutes')->nullable();
            $table->enum('sla_status', ['TEPAT', 'LEBIH', 'NA'])->default('NA')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiket');
    }
};
