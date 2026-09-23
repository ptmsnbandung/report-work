<?php

namespace App\Services;

use App\Models\ManuverCore;
use App\Models\Tiket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ManuverCoreService
{
    /**
     * Tambah record manuver core untuk tiket
     */
    public function addManuver(Tiket $tiket, array $data): ManuverCore
    {
        return DB::transaction(function () use ($tiket, $data) {
            $manuver = ManuverCore::create([
                'id_tiket' => $tiket->id,
                'titik' => strtoupper(trim($data['titik'])),
                'core_asal' => trim($data['core_asal']),
                'core_tujuan' => trim($data['core_tujuan']),
                'tipe' => strtoupper(trim($data['tipe'])),
            ]);

            // Auto-update status tiket jadi PROSES jika masih OPEN
            if ($tiket->status === 'OPEN') {
                $tiket->update(['status' => 'PROSES']);
            }

            return $manuver;
        });
    }

    /**
     * Hapus record manuver core
     */
    public function deleteManuver(ManuverCore $manuver): bool
    {
        return (bool) $manuver->delete();
    }

    /**
     * Ambil data perbandingan manuver core (dikelompokkan berdasarkan titik dan tipe)
     *
     * @param Tiket $tiket
     * @return Collection
     */
    public function getGroupedManuver(Tiket $tiket): Collection
    {
        return $tiket->manuverCores()
            ->orderBy('titik')
            ->orderBy('tipe')
            ->orderBy('id')
            ->get()
            ->groupBy('titik');
    }
}
