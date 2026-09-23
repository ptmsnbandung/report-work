<?php

namespace Database\Seeders;

use App\Models\Dokumentasi;
use App\Models\Kronologis;
use App\Models\ManuverCore;
use App\Models\Material;
use App\Models\ResumePekerjaan;
use App\Models\Tiket;
use App\Models\TitikPerbaikan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TiketDummySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $helpdesk = User::where('role', 'helpdesk')->first();
        $teknis1 = User::where('email', 'teknis@connecti.id')->first();
        $teknis2 = User::where('email', 'teknis2@connecti.id')->first();

        $tickets = [
            [
                'no_tiket' => 'BDG-20260914-001',
                'status_link_impact' => 'Backbone : SW BBLU - SW Reog Down',
                'backbone_segment' => 'SW BBLU - SW Reog',
                'deskripsi' => 'LOS pada segmen backbone BBLU arah Reog. Indikasi kabel putus di jalur crossing.',
                'tanggal_open' => Carbon::create(2026, 9, 14, 21, 0, 0),
                'tanggal_close' => Carbon::create(2026, 9, 15, 2, 45, 0),
                'status' => 'CLOSE',
                'mttr_minutes' => 345, // 5 jam 45 menit
                'sla_target_minutes' => 360, // 6 jam
                'sla_status' => 'TEPAT',
                'created_by' => $helpdesk->id,
                'closed_by' => $helpdesk->id,
            ],
            [
                'no_tiket' => 'BDG-20260915-001',
                'status_link_impact' => 'Backbone : SW Cikutra - SW Dago High Latency & Packet Loss',
                'backbone_segment' => 'SW Cikutra - SW Dago',
                'deskripsi' => 'Bending terdeteksi di KM 4.2. Redaman melonjak drastis hingga -28 dB.',
                'tanggal_open' => Carbon::create(2026, 9, 15, 8, 30, 0),
                'tanggal_close' => Carbon::create(2026, 9, 15, 16, 0, 0),
                'status' => 'CLOSE',
                'mttr_minutes' => 450, // 7 jam 30 menit
                'sla_target_minutes' => 360, // 6 jam
                'sla_status' => 'LEBIH',
                'created_by' => $helpdesk->id,
                'closed_by' => $admin->id,
            ],
            [
                'no_tiket' => 'BDG-20260916-001',
                'status_link_impact' => 'Segment : SW Pasteur - SW Sukajadi FO Cut',
                'backbone_segment' => 'SW Pasteur - SW Sukajadi',
                'deskripsi' => 'Pekerjaan galian drainase kota mengenai kabel fiber optic 24 core.',
                'tanggal_open' => Carbon::create(2026, 9, 16, 10, 15, 0),
                'tanggal_close' => Carbon::create(2026, 9, 16, 13, 30, 0),
                'status' => 'CLOSE',
                'mttr_minutes' => 195, // 3 jam 15 menit
                'sla_target_minutes' => 240, // 4 jam
                'sla_status' => 'TEPAT',
                'created_by' => $helpdesk->id,
                'closed_by' => $helpdesk->id,
            ],
            [
                'no_tiket' => 'BDG-20260916-002',
                'status_link_impact' => 'Backbone : SW Soekarno Hatta - SW Buah Batu Total Down',
                'backbone_segment' => 'SW Soekarno Hatta - SW Buah Batu',
                'deskripsi' => 'Tiang miring tertabrak truk box di perempatan bypass.',
                'tanggal_open' => Carbon::create(2026, 9, 16, 14, 0, 0),
                'tanggal_close' => Carbon::create(2026, 9, 16, 19, 0, 0),
                'status' => 'CLOSE',
                'mttr_minutes' => 300,
                'sla_target_minutes' => 360,
                'sla_status' => 'TEPAT',
                'created_by' => $helpdesk->id,
                'closed_by' => $helpdesk->id,
            ],
            [
                'no_tiket' => 'BDG-20260917-001',
                'status_link_impact' => 'Segment : SW Kopo - SW Cibaduyut Core Degradation',
                'backbone_segment' => 'SW Kopo - SW Cibaduyut',
                'deskripsi' => 'Redaman tinggi di closure lama dekat jembatan tol.',
                'tanggal_open' => Carbon::create(2026, 9, 17, 9, 0, 0),
                'tanggal_close' => null,
                'status' => 'PROSES',
                'mttr_minutes' => null,
                'sla_target_minutes' => 180,
                'sla_status' => 'NA',
                'created_by' => $helpdesk->id,
                'closed_by' => null,
            ],
            [
                'no_tiket' => 'BDG-20260917-002',
                'status_link_impact' => 'Segment : SW Ujungberung - SW Cibiru Link Flapping',
                'backbone_segment' => 'SW Ujungberung - SW Cibiru',
                'deskripsi' => 'Link sering up-down setiap 15 menit, indikasi patchcord kotor atau loose tube.',
                'tanggal_open' => Carbon::create(2026, 9, 17, 13, 30, 0),
                'tanggal_close' => null,
                'status' => 'PROSES',
                'mttr_minutes' => null,
                'sla_target_minutes' => 180,
                'sla_status' => 'NA',
                'created_by' => $helpdesk->id,
                'closed_by' => null,
            ],
            [
                'no_tiket' => 'BDG-20260918-001',
                'status_link_impact' => 'Backbone : SW Cimahi - SW Padalarang Cut KM 11',
                'backbone_segment' => 'SW Cimahi - SW Padalarang',
                'deskripsi' => 'Kabel 48 core putus tersangkut dahan pohon tumbang saat hujan lebat.',
                'tanggal_open' => Carbon::create(2026, 9, 18, 6, 15, 0),
                'tanggal_close' => null,
                'status' => 'OPEN',
                'mttr_minutes' => null,
                'sla_target_minutes' => 240,
                'sla_status' => 'NA',
                'created_by' => $helpdesk->id,
                'closed_by' => null,
            ],
            [
                'no_tiket' => 'BDG-20260918-002',
                'status_link_impact' => 'Segment : SW Setiabudi - SW Lembang Optical Signal Lost',
                'backbone_segment' => 'SW Setiabudi - SW Lembang',
                'deskripsi' => 'LOS pada OLT Lembang, traffic terisolir total.',
                'tanggal_open' => Carbon::create(2026, 9, 18, 8, 45, 0),
                'tanggal_close' => null,
                'status' => 'OPEN',
                'mttr_minutes' => null,
                'sla_target_minutes' => 300,
                'sla_status' => 'NA',
                'created_by' => $admin->id,
                'closed_by' => null,
            ],
            [
                'no_tiket' => 'BDG-20260918-003',
                'status_link_impact' => 'Backbone : SW BBLU - SW Reog Secondary Path Bending',
                'backbone_segment' => 'SW BBLU - SW Reog',
                'deskripsi' => 'Jalur proteksi redaman naik ke -25dB setelah badai semalam.',
                'tanggal_open' => Carbon::create(2026, 9, 18, 9, 30, 0),
                'tanggal_close' => null,
                'status' => 'PROSES',
                'mttr_minutes' => null,
                'sla_target_minutes' => 360,
                'sla_status' => 'NA',
                'created_by' => $helpdesk->id,
                'closed_by' => null,
            ],
            [
                'no_tiket' => 'BDG-20260918-004',
                'status_link_impact' => 'Segment : SW Kopo - SW Cibaduyut Closure Water Ingress',
                'backbone_segment' => 'SW Kopo - SW Cibaduyut',
                'deskripsi' => 'Air masuk ke closure tiang 14 menyebabkan redaman tinggi pada core 1-6.',
                'tanggal_open' => Carbon::create(2026, 9, 18, 10, 0, 0),
                'tanggal_close' => null,
                'status' => 'OPEN',
                'mttr_minutes' => null,
                'sla_target_minutes' => 180,
                'sla_status' => 'NA',
                'created_by' => $helpdesk->id,
                'closed_by' => null,
            ],
        ];

        foreach ($tickets as $t) {
            $tiket = Tiket::updateOrCreate(
                ['no_tiket' => $t['no_tiket']],
                $t
            );

            // Jika tiket closed pertama (BDG-20260914-001) - buat kronologis & resume lengkap sesuai referensi brief
            if ($tiket->no_tiket === 'BDG-20260914-001') {
                $kronologisList = [
                    [
                        'id_tiket' => $tiket->id,
                        'timestamp' => Carbon::create(2026, 9, 14, 21, 15, 0),
                        'user_id' => $teknis1->id,
                        'kategori' => 'IZIN',
                        'informasi' => 'Tim teknis izin bergerak menuju lokasi segment SW BBLU.',
                        'latitude' => -6.914744,
                        'longitude' => 107.609810,
                    ],
                    [
                        'id_tiket' => $tiket->id,
                        'timestamp' => Carbon::create(2026, 9, 14, 21, 45, 0),
                        'user_id' => $teknis1->id,
                        'kategori' => 'OTDR',
                        'informasi' => 'Hasil ukur OTDR dari SW BBLU putus di 4.8 KM arah Reog.',
                        'latitude' => -6.918900,
                        'longitude' => 107.615200,
                    ],
                    [
                        'id_tiket' => $tiket->id,
                        'timestamp' => Carbon::create(2026, 9, 14, 22, 30, 0),
                        'user_id' => $teknis2->id,
                        'kategori' => 'TRACING',
                        'informasi' => 'Titik putus ditemukan di perempatan jalan raya, kabel ditarik truk tronton kontainer.',
                        'latitude' => -6.379199,
                        'longitude' => 106.846552,
                    ],
                    [
                        'id_tiket' => $tiket->id,
                        'timestamp' => Carbon::create(2026, 9, 14, 23, 10, 0),
                        'user_id' => $teknis1->id,
                        'kategori' => 'MATERIAL',
                        'informasi' => 'Material tambahan kabel 24 core 150m dan 2 unit New JC 24C tiba di lokasi.',
                        'latitude' => -6.379199,
                        'longitude' => 106.846552,
                    ],
                    [
                        'id_tiket' => $tiket->id,
                        'timestamp' => Carbon::create(2026, 9, 15, 0, 30, 0),
                        'user_id' => $teknis1->id,
                        'kategori' => 'JOINTING',
                        'informasi' => 'Proses splicing jointing closure 1 (JC1) selesai, lanjut penarikan kabel ke JC2.',
                        'latitude' => -6.379199,
                        'longitude' => 106.846552,
                    ],
                    [
                        'id_tiket' => $tiket->id,
                        'timestamp' => Carbon::create(2026, 9, 15, 1, 45, 0),
                        'user_id' => $teknis2->id,
                        'kategori' => 'JOINTING',
                        'informasi' => 'Splicing closure 2 (JC2) selesai. Semua core 1 s/d 24 tersambung.',
                        'latitude' => -6.379578,
                        'longitude' => 106.846475,
                    ],
                    [
                        'id_tiket' => $tiket->id,
                        'timestamp' => Carbon::create(2026, 9, 15, 2, 15, 0),
                        'user_id' => $teknis1->id,
                        'kategori' => 'LINK_UP',
                        'informasi' => 'Link backbone BBLU - Reog terverifikasi UP normal. Redaman rata-rata -18.2 dB.',
                        'latitude' => -6.379578,
                        'longitude' => 106.846475,
                    ],
                    [
                        'id_tiket' => $tiket->id,
                        'timestamp' => Carbon::create(2026, 9, 15, 2, 35, 0),
                        'user_id' => $teknis2->id,
                        'kategori' => 'SELESAI',
                        'informasi' => 'Pemasangan closure ke tiang selesai, perapihan kabel dan pembersihan lokasi.',
                        'latitude' => -6.379578,
                        'longitude' => 106.846475,
                    ],
                ];

                foreach ($kronologisList as $k) {
                    Kronologis::create($k);
                }

                // Resume
                ResumePekerjaan::create([
                    'id_tiket' => $tiket->id,
                    'team_om' => ['Rian Suryana', 'Budi Santoso', 'Dedi Supriadi'],
                    'problem_temuan' => 'Kabel Tertarik Truk di Crossingan Jalan akibat ketinggian kabel turun.',
                    'action' => 'Jumper Kabel 150 meter, Pemasangan New JC 2 Titik (JC1 dan JC2) serta perkuatan tarikan span tiang.',
                    'catatan_tambahan' => 'Diperlukan pengajuan tiang peninggi baru ke dinas PU agar tidak tersangkut kendaraan besar di masa mendatang.',
                ]);

                // Material
                Material::create([
                    'id_tiket' => $tiket->id,
                    'nama_material' => 'Joint Closure (JC) 24 Core',
                    'jumlah' => 2,
                    'satuan' => 'pcs',
                ]);
                Material::create([
                    'id_tiket' => $tiket->id,
                    'nama_material' => 'Kabel Fiber Optic Aerial 24 Core',
                    'jumlah' => 150,
                    'satuan' => 'meter',
                ]);
                Material::create([
                    'id_tiket' => $tiket->id,
                    'nama_material' => 'Protection Sleeve 60mm',
                    'jumlah' => 48,
                    'satuan' => 'pcs',
                ]);

                // Titik Perbaikan (JC1 & JC2)
                TitikPerbaikan::create([
                    'id_tiket' => $tiket->id,
                    'nama_titik' => 'JC1 (Jointing Closure 1)',
                    'latitude' => -6.379199,
                    'longitude' => 106.846552,
                    'keterangan' => 'Tiang No 18 depan ruko',
                ]);
                TitikPerbaikan::create([
                    'id_tiket' => $tiket->id,
                    'nama_titik' => 'JC2 (Jointing Closure 2)',
                    'latitude' => -6.379578,
                    'longitude' => 106.846475,
                    'keterangan' => 'Tiang No 22 seberang SPBU',
                ]);

                // Manuver Core
                ManuverCore::create([
                    'id_tiket' => $tiket->id,
                    'titik' => 'JC1',
                    'core_asal' => 'Tube 2 Core 1',
                    'core_tujuan' => 'Tube 2 Core 1',
                    'tipe' => 'SEBELUM',
                ]);
                ManuverCore::create([
                    'id_tiket' => $tiket->id,
                    'titik' => 'JC1',
                    'core_asal' => 'Tube 2 Core 7',
                    'core_tujuan' => 'Tube 2 Core 7',
                    'tipe' => 'SESUDAH',
                ]);
            }

            // Jika tiket kedua in-progress (BDG-20260917-001) - buat kronologis in progress
            if ($tiket->no_tiket === 'BDG-20260917-001') {
                Kronologis::create([
                    'id_tiket' => $tiket->id,
                    'timestamp' => Carbon::create(2026, 9, 17, 9, 30, 0),
                    'user_id' => $teknis1->id,
                    'kategori' => 'IZIN',
                    'informasi' => 'Izin masuk lokasi gardu Kopo untuk pengecekan ODF.',
                    'latitude' => -6.950000,
                    'longitude' => 107.580000,
                ]);
                Kronologis::create([
                    'id_tiket' => $tiket->id,
                    'timestamp' => Carbon::create(2026, 9, 17, 10, 15, 0),
                    'user_id' => $teknis1->id,
                    'kategori' => 'OTDR',
                    'informasi' => 'Ditemukan microbending di closure KM 2.3 dekat gerbang tol.',
                    'latitude' => -6.952000,
                    'longitude' => 107.584000,
                ]);
            }
        }
    }
}
