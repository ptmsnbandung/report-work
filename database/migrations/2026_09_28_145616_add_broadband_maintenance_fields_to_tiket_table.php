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
        Schema::table('tiket', function (Blueprint $table) {
            $table->string('kategori_tiket', 30)->default('BACKBONE')->after('no_tiket')->index();
            $table->string('id_pelanggan', 50)->nullable()->after('kategori_tiket')->index();
            $table->string('nama_pelanggan', 200)->nullable()->after('id_pelanggan')->index();
            $table->string('no_kontak_pelanggan', 50)->nullable()->after('nama_pelanggan');
            $table->text('alamat_pelanggan')->nullable()->after('no_kontak_pelanggan');
            $table->string('titik_odp', 100)->nullable()->after('alamat_pelanggan');
            $table->string('kode_bandwith', 50)->nullable()->after('titik_odp');
            $table->string('kode_pop', 50)->nullable()->after('kode_bandwith');
            $table->text('lon_lat_pelanggan')->nullable()->after('kode_pop');
            $table->string('sn_ont', 100)->nullable()->after('lon_lat_pelanggan');
            $table->string('jenis_kendala_broadband', 100)->nullable()->after('sn_ont');
            $table->string('redaman_sebelum', 20)->nullable()->after('jenis_kendala_broadband');
            $table->string('redaman_sesudah', 20)->nullable()->after('redaman_sebelum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tiket', function (Blueprint $table) {
            $table->dropIndex(['kategori_tiket']);
            $table->dropIndex(['id_pelanggan']);
            $table->dropIndex(['nama_pelanggan']);
            $table->dropColumn([
                'kategori_tiket',
                'id_pelanggan',
                'nama_pelanggan',
                'no_kontak_pelanggan',
                'alamat_pelanggan',
                'titik_odp',
                'kode_bandwith',
                'kode_pop',
                'lon_lat_pelanggan',
                'sn_ont',
                'jenis_kendala_broadband',
                'redaman_sebelum',
                'redaman_sesudah',
            ]);
        });
    }
};
