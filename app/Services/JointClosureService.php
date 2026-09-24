<?php

namespace App\Services;

use App\Models\Tiket;
use App\Models\TiketJointClosure;
use App\Models\TiketJointClosureCore;
use Illuminate\Support\Facades\DB;

class JointClosureService
{
    /**
     * Simpan data Joint Closure beserta daftar sambungan core-nya
     */
    public function store(Tiket $tiket, array $data, ?int $userId = null): TiketJointClosure
    {
        return DB::transaction(function () use ($tiket, $data, $userId) {
            $isAsetBaru = !empty($data['is_aset_baru']) || ($data['status_aset'] ?? '') === 'ASET_BARU';

            $jc = TiketJointClosure::create([
                'id_tiket' => $tiket->id,
                'nama_closure' => $data['nama_closure'],
                'tipe_closure' => $data['tipe_closure'] ?? $data['jenis_closure'] ?? 'DOME',
                'status_aset' => $isAsetBaru ? 'ASET_BARU' : 'EKSISTING',
                'kapasitas_kabel_asal' => (int) ($data['kapasitas_kabel_asal'] ?? 96),
                'jumlah_tube_asal' => (int) ($data['jumlah_tube_asal'] ?? 8),
                'kapasitas_kabel_jumper' => (int) ($data['kapasitas_kabel_jumper'] ?? 48),
                'jumlah_tube_jumper' => (int) ($data['jumlah_tube_jumper'] ?? 4),
                'jenis_sambungan' => $data['jenis_sambungan'] ?? 'LURUS_STRAIGHT',
                'lokasi_penempatan' => $data['lokasi_penempatan'] ?? $data['lokasi_fisik'] ?? 'POLE',
                'latitude' => !empty($data['latitude']) ? (float) $data['latitude'] : null,
                'longitude' => !empty($data['longitude']) ? (float) $data['longitude'] : null,
                'catatan' => $data['catatan'] ?? $data['keterangan'] ?? null,
                'created_by' => $userId,
            ]);

            // 1. Format array cores associative (API format)
            if (!empty($data['cores']) && is_array($data['cores'])) {
                foreach ($data['cores'] as $coreRow) {
                    if (!empty($coreRow['tube_asal']) && !empty($coreRow['core_asal'])) {
                        TiketJointClosureCore::create([
                            'id_joint_closure' => $jc->id,
                            'tube_asal' => $coreRow['tube_asal'],
                            'core_asal' => $coreRow['core_asal'],
                            'tube_tujuan' => $coreRow['tube_tujuan'] ?? $coreRow['tube_jumper'] ?? null,
                            'core_tujuan' => $coreRow['core_tujuan'] ?? $coreRow['core_jumper'] ?? null,
                            'status_core' => $coreRow['status_core'] ?? $coreRow['status'] ?? 'TERHUBUNG',
                            'loss_db' => !empty($coreRow['loss_db']) ? (float) $coreRow['loss_db'] : null,
                            'keterangan' => $coreRow['keterangan'] ?? null,
                        ]);
                    }
                }
            }

            // 2. Format HTML form multi-row inputs (tube_asal[], core_asal[], etc.)
            if (!empty($data['tube_asal']) && is_array($data['tube_asal'])) {
                foreach ($data['tube_asal'] as $idx => $tAsal) {
                    $cAsal = $data['core_asal'][$idx] ?? null;
                    if (!empty($tAsal) && !empty($cAsal)) {
                        $tJumper = $data['tube_jumper'][$idx] ?? $data['tube_tujuan'][$idx] ?? null;
                        $cJumper = $data['core_jumper'][$idx] ?? $data['core_tujuan'][$idx] ?? null;
                        $status = $data['core_status'][$idx] ?? $data['status_core'][$idx] ?? $data['status'][$idx] ?? 'TERHUBUNG';
                        $loss = !empty($data['loss_db'][$idx]) ? (float) $data['loss_db'][$idx] : null;
                        $ket = $data['core_keterangan'][$idx] ?? $data['keterangan'][$idx] ?? null;

                        TiketJointClosureCore::create([
                            'id_joint_closure' => $jc->id,
                            'tube_asal' => $tAsal,
                            'core_asal' => $cAsal,
                            'tube_tujuan' => $tJumper,
                            'core_tujuan' => $cJumper,
                            'status_core' => $status,
                            'loss_db' => $loss,
                            'keterangan' => $ket,
                        ]);
                    }
                }
            }

            return $jc;
        });
    }

    /**
     * Tambah satu baris sambungan core pada Joint Closure yang sudah ada
     */
    public function addCore(TiketJointClosure $jc, array $data): TiketJointClosureCore
    {
        return TiketJointClosureCore::create([
            'id_joint_closure' => $jc->id,
            'tube_asal' => $data['tube_asal'],
            'core_asal' => $data['core_asal'],
            'tube_tujuan' => $data['tube_tujuan'] ?? $data['tube_jumper'] ?? null,
            'core_tujuan' => $data['core_tujuan'] ?? $data['core_jumper'] ?? null,
            'status_core' => $data['status_core'] ?? $data['status'] ?? 'TERHUBUNG',
            'loss_db' => !empty($data['loss_db']) ? (float) $data['loss_db'] : null,
            'keterangan' => $data['keterangan'] ?? null,
        ]);
    }

    /**
     * Hapus satu baris sambungan core
     */
    public function deleteCore(TiketJointClosureCore $core): bool
    {
        return (bool) $core->delete();
    }

    /**
     * Hapus seluruh data Joint Closure dan sambungan core di dalamnya
     */
    public function delete(TiketJointClosure $jc): bool
    {
        return (bool) $jc->delete();
    }
}
