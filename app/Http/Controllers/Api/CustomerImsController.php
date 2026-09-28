<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerImsController extends Controller
{
    /**
     * Cari data pelanggan dari database IMS (trx_batchjob_register & m_pelanggan)
     */
    public function search(Request $request): JsonResponse
    {
        $query = trim((string) $request->input('q', ''));

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'Ketik minimal 2 karakter untuk mencari pelanggan.',
            ]);
        }

        try {
            // Periksa apakah tabel trx_batchjob_register dapat diakses di koneksi ims
            $customers = DB::connection('ims')
                ->table('trx_batchjob_register as r')
                ->leftJoin('m_pelanggan as m', 'r.nik_penduduk', '=', 'm.nik_penduduk')
                ->leftJoin('m_bandwith as b', 'r.kode_bandwith', '=', 'b.kode_bandwith')
                ->select([
                    'r.nomor_internet',
                    'r.nama_pelanggan',
                    'r.alamat_pasang',
                    'r.rt_pasang',
                    'r.rw_pasang',
                    'r.nomor_bangunan',
                    'r.lon_lat',
                    'r.loc_maps',
                    'r.kode_bandwith',
                    'b.nominal_bandwith',
                    'r.kode_pop',
                    'r.olt',
                    'r.index_olt',
                    'r.media_akses',
                    DB::raw("COALESCE(NULLIF(m.nomor_hp, ''), NULLIF(m.nomor_hp_2, ''), '') as nomor_hp"),
                    'm.email as email_pelanggan',
                ])
                ->where(function ($q) use ($query) {
                    $q->where('r.nomor_internet', 'like', "%{$query}%")
                      ->orWhere('r.nama_pelanggan', 'like', "%{$query}%")
                      ->orWhere('r.alamat_pasang', 'like', "%{$query}%")
                      ->orWhere('r.nik_penduduk', 'like', "%{$query}%")
                      ->orWhere('m.nomor_hp', 'like', "%{$query}%");
                })
                ->where('r.hide', '!=', '1')
                ->limit(25)
                ->get();

            $formatted = $customers->map(function ($c) {
                $alamatLengkap = trim($c->alamat_pasang ?: '');
                $rtRw = [];
                if (!empty($c->rt_pasang) && $c->rt_pasang !== '00') $rtRw[] = 'RT ' . $c->rt_pasang;
                if (!empty($c->rw_pasang) && $c->rw_pasang !== '00') $rtRw[] = 'RW ' . $c->rw_pasang;
                if (!empty($c->nomor_bangunan) && $c->nomor_bangunan !== '00') $rtRw[] = 'No. ' . $c->nomor_bangunan;
                if (!empty($rtRw)) {
                    $alamatLengkap .= ' (' . implode(', ', $rtRw) . ')';
                }

                $odpInfo = trim(($c->olt ?: '') . ' ' . ($c->index_olt ?: ''));
                $gps = trim($c->lon_lat ?: ($c->loc_maps ?: ''));

                $nominalBw = trim((string) ($c->nominal_bandwith ?? ''));
                $bwDisplay = $nominalBw !== '' 
                    ? (is_numeric($nominalBw) ? $nominalBw . ' Mbps' : $nominalBw) 
                    : (string) ($c->kode_bandwith ?: '');

                return [
                    'id_pelanggan' => (string) $c->nomor_internet,
                    'nama_pelanggan' => (string) $c->nama_pelanggan,
                    'no_kontak' => (string) $c->nomor_hp,
                    'alamat' => $alamatLengkap,
                    'alamat_pelanggan' => $alamatLengkap,
                    'alamat_pasang' => (string) ($c->alamat_pasang ?: ''),
                    'titik_odp' => $odpInfo ?: ($c->kode_pop ?: ''),
                    'kode_bandwith' => $bwDisplay,
                    'nominal_bandwith' => $nominalBw,
                    'raw_kode_bandwith' => (string) ($c->kode_bandwith ?: ''),
                    'kode_pop' => (string) ($c->kode_pop ?: ''),
                    'lon_lat' => (string) $gps,
                    'loc_maps' => (string) ($c->loc_maps ?: ''),
                    'media_akses' => (string) ($c->media_akses ?: 'Fiber Optik'),
                    'olt' => (string) ($c->olt ?: ''),
                    'index_olt' => (string) ($c->index_olt ?: ''),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formatted,
                'count' => $formatted->count(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal mengambil data pelanggan dari IMS: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Koneksi ke database IMS (ims_v3) bermasalah: ' . $e->getMessage(),
                'data' => [],
            ], 200); // return 200 with empty data and message so frontend UX does not crash
        }
    }

    /**
     * Dapatkan detail satu pelanggan berdasarkan nomor internet
     */
    public function show(string $nomorInternet): JsonResponse
    {
        try {
            $customer = DB::connection('ims')
                ->table('trx_batchjob_register as r')
                ->leftJoin('m_pelanggan as m', 'r.nik_penduduk', '=', 'm.nik_penduduk')
                ->leftJoin('m_bandwith as b', 'r.kode_bandwith', '=', 'b.kode_bandwith')
                ->select([
                    'r.nomor_internet',
                    'r.nama_pelanggan',
                    'r.alamat_pasang',
                    'r.rt_pasang',
                    'r.rw_pasang',
                    'r.nomor_bangunan',
                    'r.lon_lat',
                    'r.loc_maps',
                    'r.kode_bandwith',
                    'b.nominal_bandwith',
                    'r.kode_pop',
                    'r.olt',
                    'r.index_olt',
                    'r.media_akses',
                    DB::raw("COALESCE(NULLIF(m.nomor_hp, ''), NULLIF(m.nomor_hp_2, ''), '') as nomor_hp"),
                    'm.email as email_pelanggan',
                ])
                ->where('r.nomor_internet', $nomorInternet)
                ->first();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pelanggan dengan CID ' . $nomorInternet . ' tidak ditemukan di IMS.',
                ], 404);
            }

            $alamatLengkap = trim($customer->alamat_pasang ?: '');
            $rtRw = [];
            if (!empty($customer->rt_pasang) && $customer->rt_pasang !== '00') $rtRw[] = 'RT ' . $customer->rt_pasang;
            if (!empty($customer->rw_pasang) && $customer->rw_pasang !== '00') $rtRw[] = 'RW ' . $customer->rw_pasang;
            if (!empty($customer->nomor_bangunan) && $customer->nomor_bangunan !== '00') $rtRw[] = 'No. ' . $customer->nomor_bangunan;
            if (!empty($rtRw)) {
                $alamatLengkap .= ' (' . implode(', ', $rtRw) . ')';
            }

            $odpInfo = trim(($customer->olt ?: '') . ' ' . ($customer->index_olt ?: ''));
            $gps = trim($customer->lon_lat ?: ($customer->loc_maps ?: ''));

            $nominalBw = trim((string) ($customer->nominal_bandwith ?? ''));
            $bwDisplay = $nominalBw !== '' 
                ? (is_numeric($nominalBw) ? $nominalBw . ' Mbps' : $nominalBw) 
                : (string) ($customer->kode_bandwith ?: '');

            return response()->json([
                'success' => true,
                'data' => [
                    'id_pelanggan' => (string) $customer->nomor_internet,
                    'nama_pelanggan' => (string) $customer->nama_pelanggan,
                    'no_kontak' => (string) $customer->nomor_hp,
                    'alamat' => $alamatLengkap,
                    'alamat_pelanggan' => $alamatLengkap,
                    'alamat_pasang' => (string) ($customer->alamat_pasang ?: ''),
                    'titik_odp' => $odpInfo ?: ($customer->kode_pop ?: ''),
                    'kode_bandwith' => $bwDisplay,
                    'nominal_bandwith' => $nominalBw,
                    'raw_kode_bandwith' => (string) ($customer->kode_bandwith ?: ''),
                    'kode_pop' => (string) ($customer->kode_pop ?: ''),
                    'lon_lat' => (string) $gps,
                    'loc_maps' => (string) ($customer->loc_maps ?: ''),
                    'media_akses' => (string) ($customer->media_akses ?: 'Fiber Optik'),
                    'olt' => (string) ($customer->olt ?: ''),
                    'index_olt' => (string) ($customer->index_olt ?: ''),
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung ke database IMS: ' . $e->getMessage(),
            ], 500);
        }
    }
}
