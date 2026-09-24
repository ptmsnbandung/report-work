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
        // 1. Tabel Pencatatan Kabel & Joint Closure (JC) / Sambungan Aset Fisik
        Schema::create('tiket_joint_closures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tiket')->constrained('tiket')->onDelete('cascade');
            $table->string('nama_closure', 100)->comment('Nama/Kode Closure, misal JC-BBLU-04A');
            $table->enum('tipe_closure', ['DOME', 'INLINE', 'BOX_FAT', 'OTB', 'CLOSURE_STANDAR'])->default('DOME');
            $table->enum('status_aset', ['EKSISTING', 'ASET_BARU'])->default('EKSISTING')->comment('ASET_BARU jika ada pemotongan & closure baru');
            
            // Spesifikasi Kapasitas Kabel Eksisting & Jumper
            $table->unsignedSmallInteger('kapasitas_kabel_asal')->default(96)->comment('2, 12, 24, 48, 96, 144, 288 Core');
            $table->unsignedSmallInteger('jumlah_tube_asal')->default(8);
            $table->unsignedSmallInteger('kapasitas_kabel_jumper')->default(48)->comment('2, 12, 24, 48, 96, 144, 288 Core');
            $table->unsignedSmallInteger('jumlah_tube_jumper')->default(4);
            
            $table->enum('jenis_sambungan', ['LURUS_STRAIGHT', 'PERCABANGAN_BRANCH', 'LOOP_MANUVER'])->default('LURUS_STRAIGHT');
            $table->string('lokasi_penempatan', 150)->nullable()->comment('No Tiang, Manhole, Handhole');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['id_tiket', 'status_aset']);
        });

        // 2. Tabel Detail Sambungan Tube & Core (Termasuk Pencatatan Core Sisa / Spare)
        Schema::create('tiket_joint_closure_cores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_joint_closure')->constrained('tiket_joint_closures')->onDelete('cascade');
            $table->string('tube_asal', 30)->comment('Nama/No Tube Asal');
            $table->string('core_asal', 30)->comment('Nama/No Core Asal');
            $table->string('tube_tujuan', 30)->nullable()->comment('Nama/No Tube Jumper');
            $table->string('core_tujuan', 30)->nullable()->comment('Nama/No Core Jumper');
            $table->enum('status_core', ['TERHUBUNG', 'SPARE', 'LOSS_PUTUS', 'MANUVER'])->default('TERHUBUNG');
            $table->decimal('loss_db', 5, 2)->nullable()->comment('Redaman sambungan dB');
            $table->string('keterangan', 255)->nullable()->comment('Alokasi link/keterangan port');
            $table->timestamps();

            $table->index(['id_joint_closure', 'status_core']);
        });

        // 3. Perluasan Tabel Manuver Core untuk Histori Perubahan Titik & Status Aset
        Schema::table('manuver_core', function (Blueprint $table) {
            $table->enum('lokasi_tipe', ['POP', 'OTB', 'CLOSURE_LAPANGAN', 'FAT_FDT', 'LAINNYA'])->default('CLOSURE_LAPANGAN')->after('id_tiket');
            $table->string('core_dialihkan', 50)->nullable()->after('core_tujuan')->comment('Core cadangan yang digunakan');
            $table->string('titik_kembali', 100)->nullable()->after('core_dialihkan')->comment('Titik normalisasi jalur');
            $table->enum('status_manuver', ['TEMPORARY', 'PERMANENT', 'RESTORED'])->default('TEMPORARY')->after('tipe');
            $table->enum('status_core_aset', ['OCCUPIED_MANUVER', 'BROKEN_LOSS', 'SPARE_AVAILABLE'])->default('OCCUPIED_MANUVER')->after('status_manuver');
            $table->text('keterangan')->nullable()->after('status_core_aset');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manuver_core', function (Blueprint $table) {
            $table->dropColumn([
                'lokasi_tipe',
                'core_dialihkan',
                'titik_kembali',
                'status_manuver',
                'status_core_aset',
                'keterangan',
            ]);
        });

        Schema::dropIfExists('tiket_joint_closure_cores');
        Schema::dropIfExists('tiket_joint_closures');
    }
};
