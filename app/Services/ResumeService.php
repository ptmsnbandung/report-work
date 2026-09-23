<?php

namespace App\Services;

use App\Models\Material;
use App\Models\ResumePekerjaan;
use App\Models\Tiket;
use App\Models\TitikPerbaikan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ResumeService
{
    /**
     * Simpan atau update resume pekerjaan tiket
     */
    public function saveOrUpdateResume(Tiket $tiket, array $data): ResumePekerjaan
    {
        return DB::transaction(function () use ($tiket, $data) {
            $teamOm = $data['team_om'];
            if (is_string($teamOm)) {
                // Split by newline or comma
                $teamOm = array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', $teamOm))));
            }

            $resume = ResumePekerjaan::updateOrCreate(
                ['id_tiket' => $tiket->id],
                [
                    'team_om'          => $teamOm,
                    'problem_temuan'   => $data['problem_temuan'],
                    'action'           => $data['action'],
                    'catatan_tambahan' => $data['catatan_tambahan'] ?? null,
                ]
            );

            // Jika status masih OPEN, ubah ke PROSES
            if ($tiket->status === 'OPEN') {
                $tiket->update(['status' => 'PROSES']);
            }

            return $resume;
        });
    }

    /**
     * Tambah material yang digunakan pada tiket
     */
    public function addMaterial(Tiket $tiket, array $data): Material
    {
        return Material::create([
            'id_tiket'      => $tiket->id,
            'nama_material' => $data['nama_material'],
            'jumlah'        => (int) $data['jumlah'],
            'satuan'        => $data['satuan'],
        ]);
    }

    /**
     * Hapus material
     */
    public function deleteMaterial(Material $material, User $user): bool
    {
        if (!$user->hasRole(['admin', 'helpdesk', 'teknis'])) {
            throw new InvalidArgumentException('Anda tidak memiliki hak akses untuk menghapus material.');
        }

        return (bool) $material->delete();
    }

    /**
     * Tambah titik penanganan / perbaikan (JC1, JC2, dll)
     */
    public function addTitikPerbaikan(Tiket $tiket, array $data): TitikPerbaikan
    {
        return TitikPerbaikan::create([
            'id_tiket'   => $tiket->id,
            'nama_titik' => $data['nama_titik'],
            'latitude'   => (float) $data['latitude'],
            'longitude'  => (float) $data['longitude'],
            'keterangan' => $data['keterangan'] ?? null,
        ]);
    }

    /**
     * Hapus titik perbaikan
     */
    public function deleteTitikPerbaikan(TitikPerbaikan $titik, User $user): bool
    {
        if (!$user->hasRole(['admin', 'helpdesk', 'teknis'])) {
            throw new InvalidArgumentException('Anda tidak memiliki hak akses untuk menghapus titik perbaikan.');
        }

        return (bool) $titik->delete();
    }
}
