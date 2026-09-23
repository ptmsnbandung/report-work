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
        $this->addIndexIfNotExists('tiket', ['status', 'tanggal_open'], 'idx_tiket_status_tanggal_open');
        $this->addIndexIfNotExists('tiket', ['backbone_segment', 'status'], 'idx_tiket_segment_status');
        $this->addIndexIfNotExists('tiket', 'created_by', 'idx_tiket_created_by');
        $this->addIndexIfNotExists('tiket', 'closed_by', 'idx_tiket_closed_by');

        $this->addIndexIfNotExists('kronologis', ['id_tiket', 'timestamp'], 'idx_kronologis_tiket_timestamp');
        $this->addIndexIfNotExists('kronologis', ['user_id', 'created_at'], 'idx_kronologis_user_created');

        $this->addIndexIfNotExists('dokumentasi', ['id_tiket', 'timestamp'], 'idx_dokumentasi_tiket_timestamp');
        $this->addIndexIfNotExists('dokumentasi', ['id_tiket', 'kategori'], 'idx_dokumentasi_tiket_kategori');

        $this->addIndexIfNotExists('notifikasi_log', ['id_tiket', 'created_at'], 'idx_notifikasi_log_tiket_created');

        $this->addIndexIfNotExists('titik_perbaikan', ['id_tiket', 'created_at'], 'idx_titik_perbaikan_tiket_created');
        $this->addIndexIfNotExists('manuver_core', ['id_tiket', 'created_at'], 'idx_manuver_core_tiket_created');
        $this->addIndexIfNotExists('material', ['id_tiket', 'created_at'], 'idx_material_tiket_created');
    }

    /**
     * Helper to safely add index if not exists across MySQL and SQLite
     */
    protected function addIndexIfNotExists(string $table, array|string $columns, string $indexName): void
    {
        $columns = (array) $columns;

        if (!Schema::hasIndex($table, $indexName)) {
            Schema::table($table, function (Blueprint $t) use ($columns, $indexName) {
                $t->index($columns, $indexName);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexIfExists('tiket', 'idx_tiket_status_tanggal_open');
        $this->dropIndexIfExists('tiket', 'idx_tiket_segment_status');
        $this->dropIndexIfExists('tiket', 'idx_tiket_created_by');
        $this->dropIndexIfExists('tiket', 'idx_tiket_closed_by');

        $this->dropIndexIfExists('kronologis', 'idx_kronologis_tiket_timestamp');
        $this->dropIndexIfExists('kronologis', 'idx_kronologis_user_created');

        $this->dropIndexIfExists('dokumentasi', 'idx_dokumentasi_tiket_timestamp');
        $this->dropIndexIfExists('dokumentasi', 'idx_dokumentasi_tiket_kategori');

        $this->dropIndexIfExists('notifikasi_log', 'idx_notifikasi_log_tiket_created');

        $this->dropIndexIfExists('titik_perbaikan', 'idx_titik_perbaikan_tiket_created');
        $this->dropIndexIfExists('manuver_core', 'idx_manuver_core_tiket_created');
        $this->dropIndexIfExists('material', 'idx_material_tiket_created');
    }

    protected function dropIndexIfExists(string $table, string $indexName): void
    {
        if (Schema::hasIndex($table, $indexName)) {
            Schema::table($table, function (Blueprint $t) use ($indexName) {
                $t->dropIndex($indexName);
            });
        }
    }
};
