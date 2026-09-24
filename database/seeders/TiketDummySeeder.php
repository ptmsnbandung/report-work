<?php

namespace Database\Seeders;

use App\Models\Dokumentasi;
use App\Models\Kronologis;
use App\Models\ManuverCore;
use App\Models\Material;
use App\Models\ResumePekerjaan;
use App\Models\Tiket;
use App\Models\TiketHandoverShift;
use App\Models\TiketJointClosure;
use App\Models\TiketJointClosureCore;
use App\Models\TiketStopClock;
use App\Models\TitikPerbaikan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TiketDummySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::first();
        $helpdesk = User::where('role', 'helpdesk')->first() ?? $admin;
        $teknis1 = User::where('email', 'teknis@connecti.id')->first() ?? $admin;
        $teknis2 = User::where('email', 'teknis2@connecti.id')->first() ?? $admin;

        // ═════════════════════════════════════════════════════════════════════
        // 1. TIKET #1: CLOSE (LENGKAP SEMUA FITUR - JOINTING LURUS & KABEL JC)
        // ═════════════════════════════════════════════════════════════════════
        $tiket1 = Tiket::updateOrCreate(
            ['no_tiket' => 'BDG-20260914-001'],
            [
                'status_link_impact' => 'Backbone : SW BBLU - SW Reog Down',
                'backbone_segment' => 'SW BBLU - SW Reog',
                'deskripsi' => 'LOS pada segmen backbone BBLU arah Reog. Indikasi kabel putus tertabrak truk kontainer di jalur crossingan jalan raya.',
                'closing_notes_teknisi' => 'Penyambungan jointing 2 titik closure baru (JC1 & JC2) selesai. Semua core 1-24 link backbone UP normal dengan redaman rata-rata -18.2 dB.',
                'tipe_penanganan' => 'JOINTING_LURUS',
                'tanggal_open' => Carbon::create(2026, 9, 14, 21, 0, 0),
                'first_response_at' => Carbon::create(2026, 9, 14, 21, 15, 0),
                'response_time_minutes' => 15,
                'tanggal_close' => Carbon::create(2026, 9, 15, 2, 45, 0),
                'resolved_at' => Carbon::create(2026, 9, 15, 2, 35, 0),
                'status' => 'CLOSE',
                'sla_target_minutes' => 360, // 6 jam
                'mttr_minutes' => 345, // 5 jam 45 menit
                'total_stop_clock_minutes' => 30,
                'is_stop_clock' => false,
                'sla_status' => 'TEPAT',
                'created_by' => $helpdesk->id,
                'resolved_by' => $teknis1->id,
                'closed_by' => $helpdesk->id,
            ]
        );

        // Kronologis Tiket 1 (WhatsApp style timeline)
        $kronologisList1 = [
            [
                'id_tiket' => $tiket1->id,
                'timestamp' => Carbon::create(2026, 9, 14, 21, 15, 0),
                'user_id' => $teknis1->id,
                'kategori' => 'IZIN',
                'informasi' => 'Tim teknis izin bergerak menuju lokasi segment SW BBLU - SW Reog.',
                'latitude' => -6.914744,
                'longitude' => 107.609810,
            ],
            [
                'id_tiket' => $tiket1->id,
                'timestamp' => Carbon::create(2026, 9, 14, 21, 45, 0),
                'user_id' => $teknis1->id,
                'kategori' => 'OTDR',
                'informasi' => 'Hasil ukur OTDR dari OTB SW BBLU mendeteksi event putus total di jarak 4.8 KM arah Reog.',
                'latitude' => -6.918900,
                'longitude' => 107.615200,
            ],
            [
                'id_tiket' => $tiket1->id,
                'timestamp' => Carbon::create(2026, 9, 14, 22, 30, 0),
                'user_id' => $teknis2->id,
                'kategori' => 'TRACING',
                'informasi' => 'Titik putus ditemukan di perempatan jalan raya, kabel ditarik truk tronton kontainer yang melintas.',
                'latitude' => -6.379199,
                'longitude' => 106.846552,
            ],
            [
                'id_tiket' => $tiket1->id,
                'timestamp' => Carbon::create(2026, 9, 14, 23, 10, 0),
                'user_id' => $teknis1->id,
                'kategori' => 'MATERIAL',
                'informasi' => 'Material kabel jumper 24 core 150 meter dan 2 unit Joint Closure (JC) baru tiba di lokasi perbaikan.',
                'latitude' => -6.379199,
                'longitude' => 106.846552,
            ],
            [
                'id_tiket' => $tiket1->id,
                'timestamp' => Carbon::create(2026, 9, 15, 0, 30, 0),
                'user_id' => $teknis1->id,
                'kategori' => 'JOINTING',
                'informasi' => 'Proses splicing jointing closure 1 (JC-BBLU-01) selesai 24 core, lanjut penarikan kabel span ke JC-BBLU-02.',
                'latitude' => -6.379199,
                'longitude' => 106.846552,
            ],
            [
                'id_tiket' => $tiket1->id,
                'timestamp' => Carbon::create(2026, 9, 15, 1, 45, 0),
                'user_id' => $teknis2->id,
                'kategori' => 'JOINTING',
                'informasi' => 'Splicing closure 2 (JC-BBLU-02) selesai. Semua core 1 s/d 24 tersambung dengan loss rata-rata 0.03 dB.',
                'latitude' => -6.379578,
                'longitude' => 106.846475,
            ],
            [
                'id_tiket' => $tiket1->id,
                'timestamp' => Carbon::create(2026, 9, 15, 2, 15, 0),
                'user_id' => $teknis1->id,
                'kategori' => 'LINK_UP',
                'informasi' => 'Link backbone BBLU - Reog terverifikasi UP normal. Trafik stabil, redaman rata-rata -18.2 dB.',
                'latitude' => -6.379578,
                'longitude' => 106.846475,
            ],
            [
                'id_tiket' => $tiket1->id,
                'timestamp' => Carbon::create(2026, 9, 15, 2, 35, 0),
                'user_id' => $teknis2->id,
                'kategori' => 'SELESAI',
                'informasi' => 'Pemasangan closure ke tiang selesai, perapihan kabel span, dan pembersihan lokasi kerja.',
                'latitude' => -6.379578,
                'longitude' => 106.846475,
            ],
        ];

        foreach ($kronologisList1 as $k) {
            Kronologis::updateOrCreate(
                ['id_tiket' => $k['id_tiket'], 'timestamp' => $k['timestamp']],
                $k
            );
        }

        // Resume Tiket 1
        ResumePekerjaan::updateOrCreate(
            ['id_tiket' => $tiket1->id],
            [
                'tipe_penanganan' => 'JOINTING_LURUS',
                'joint_closure_type' => 'DOME 24-48 Core',
                'core_count_jointed' => 24,
                'team_om' => ['Rian Suryana (Lead)', 'Budi Santoso (Splicer)', 'Dedi Supriadi (Lineman)'],
                'problem_temuan' => 'Kabel FO Aerial 24 Core tersangkut truk box kontainer tinggi di perempatan jalan raya crossingan utama.',
                'action' => 'Penarikan jumper kabel FO 24 core sepanjang 150 meter, pemasangan 2 titik Joint Closure baru (JC1 & JC2), serta rekoneksi 24 core.',
                'catatan_tambahan' => 'Rekomendasi penambahan tiang peninggi kabel span crossing agar memenuhi batas standar keselamatan ketinggian jalan raya.',
            ]
        );

        // Material Tiket 1
        $materials1 = [
            ['nama_material' => 'Joint Closure (JC) Dome 24-48 Core', 'jumlah' => 2, 'satuan' => 'unit'],
            ['nama_material' => 'Kabel Fiber Optic Aerial 24 Core G.652D', 'jumlah' => 150, 'satuan' => 'meter'],
            ['nama_material' => 'Protection Sleeve 60mm', 'jumlah' => 48, 'satuan' => 'pcs'],
            ['nama_material' => 'Suspension Clamp & Bracket Tiang', 'jumlah' => 4, 'satuan' => 'set'],
            ['nama_material' => 'Stainless Steel Strip Band & Buckle', 'jumlah' => 6, 'satuan' => 'meter'],
        ];
        foreach ($materials1 as $m) {
            Material::updateOrCreate(['id_tiket' => $tiket1->id, 'nama_material' => $m['nama_material']], $m);
        }

        // Titik Perbaikan Tiket 1
        TitikPerbaikan::updateOrCreate(
            ['id_tiket' => $tiket1->id, 'nama_titik' => 'JC1 (Joint Closure Span Barat)'],
            [
                'latitude' => -6.379199,
                'longitude' => 106.846552,
                'keterangan' => 'Tiang No 18 depan ruko - Posisi Closure Baru Arah BBLU',
            ]
        );
        TitikPerbaikan::updateOrCreate(
            ['id_tiket' => $tiket1->id, 'nama_titik' => 'JC2 (Joint Closure Span Timur)'],
            [
                'latitude' => -6.379578,
                'longitude' => 106.846475,
                'keterangan' => 'Tiang No 22 seberang SPBU - Posisi Closure Baru Arah Reog',
            ]
        );

        // Joint Closure #1 (JC-BBLU-01) - ASET BARU (Kabel 96C disambung Jumper 48C)
        $jc1 = TiketJointClosure::updateOrCreate(
            ['id_tiket' => $tiket1->id, 'nama_closure' => 'JC-BBLU-01 (Crossing Barat)'],
            [
                'tipe_closure' => 'DOME',
                'status_aset' => 'ASET_BARU',
                'kapasitas_kabel_asal' => 96,
                'jumlah_tube_asal' => 8,
                'kapasitas_kabel_jumper' => 48,
                'jumlah_tube_jumper' => 4,
                'jenis_sambungan' => 'LURUS_STRAIGHT',
                'lokasi_penempatan' => 'Tiang FO #18 Simpang Lima',
                'latitude' => -6.379199,
                'longitude' => 106.846552,
                'catatan' => 'Pemasangan closure baru akibat pemotongan span putus. Tray 1 & Tray 2 digunakan untuk backbone utama.',
                'created_by' => $teknis1->id,
            ]
        );

        // Splicing Tray Cores untuk JC1
        $jcCoresData1 = [
            ['tube_asal' => 'Tube 1 (Biru)', 'core_asal' => 'Core 1 (Biru)', 'tube_tujuan' => 'Tube 1 (Biru)', 'core_tujuan' => 'Core 1 (Biru)', 'status_core' => 'TERHUBUNG', 'loss_db' => 0.02, 'keterangan' => 'Backbone Primary Link A'],
            ['tube_asal' => 'Tube 1 (Biru)', 'core_asal' => 'Core 2 (Oranye)', 'tube_tujuan' => 'Tube 1 (Biru)', 'core_tujuan' => 'Core 2 (Oranye)', 'status_core' => 'TERHUBUNG', 'loss_db' => 0.03, 'keterangan' => 'Backbone Primary Link B'],
            ['tube_asal' => 'Tube 1 (Biru)', 'core_asal' => 'Core 3 (Hijau)', 'tube_tujuan' => 'Tube 1 (Biru)', 'core_tujuan' => 'Core 3 (Hijau)', 'status_core' => 'TERHUBUNG', 'loss_db' => 0.01, 'keterangan' => 'Metro-E Enterprise A'],
            ['tube_asal' => 'Tube 1 (Biru)', 'core_asal' => 'Core 4 (Coklat)', 'tube_tujuan' => 'Tube 1 (Biru)', 'core_tujuan' => 'Core 4 (Coklat)', 'status_core' => 'TERHUBUNG', 'loss_db' => 0.04, 'keterangan' => 'Metro-E Enterprise B'],
            ['tube_asal' => 'Tube 1 (Biru)', 'core_asal' => 'Core 5 (Abu)', 'tube_tujuan' => 'Tube 1 (Biru)', 'core_tujuan' => 'Core 5 (Abu)', 'status_core' => 'TERHUBUNG', 'loss_db' => 0.02, 'keterangan' => 'OLT Distribusi Timur'],
            ['tube_asal' => 'Tube 1 (Biru)', 'core_asal' => 'Core 6 (Putih)', 'tube_tujuan' => 'Tube 1 (Biru)', 'core_tujuan' => 'Core 6 (Putih)', 'status_core' => 'TERHUBUNG', 'loss_db' => 0.02, 'keterangan' => 'OLT Distribusi Barat'],
            ['tube_asal' => 'Tube 2 (Oranye)', 'core_asal' => 'Core 1 (Biru)', 'tube_tujuan' => null, 'core_tujuan' => null, 'status_core' => 'SPARE', 'loss_db' => null, 'keterangan' => 'Cadangan Tray 2 - Alokasi Spare'],
            ['tube_asal' => 'Tube 2 (Oranye)', 'core_asal' => 'Core 2 (Oranye)', 'tube_tujuan' => null, 'core_tujuan' => null, 'status_core' => 'SPARE', 'loss_db' => null, 'keterangan' => 'Cadangan Tray 2 - Alokasi Spare'],
            ['tube_asal' => 'Tube 2 (Oranye)', 'core_asal' => 'Core 3 (Hijau)', 'tube_tujuan' => 'Tube 2 (Oranye)', 'core_tujuan' => 'Core 7 (Merah)', 'status_core' => 'MANUVER', 'loss_db' => 0.05, 'keterangan' => 'Swapping alokasi ke port proteksi'],
            ['tube_asal' => 'Tube 3 (Hijau)', 'core_asal' => 'Core 12 (Toska)', 'tube_tujuan' => null, 'core_tujuan' => null, 'status_core' => 'LOSS_PUTUS', 'loss_db' => 12.50, 'keterangan' => 'Core mengalami microbending bawaan'],
        ];

        foreach ($jcCoresData1 as $cd) {
            TiketJointClosureCore::updateOrCreate(
                ['id_joint_closure' => $jc1->id, 'tube_asal' => $cd['tube_asal'], 'core_asal' => $cd['core_asal']],
                $cd
            );
        }

        // Joint Closure #2 (JC-BBLU-02) - EKSISTING (Kabel 48C disambung 24C)
        $jc2 = TiketJointClosure::updateOrCreate(
            ['id_tiket' => $tiket1->id, 'nama_closure' => 'JC-BBLU-02 (Crossing Timur)'],
            [
                'tipe_closure' => 'INLINE',
                'status_aset' => 'EKSISTING',
                'kapasitas_kabel_asal' => 48,
                'jumlah_tube_asal' => 4,
                'kapasitas_kabel_jumper' => 24,
                'jumlah_tube_jumper' => 2,
                'jenis_sambungan' => 'LURUS_STRAIGHT',
                'lokasi_penempatan' => 'Tiang FO #22 Depan SPBU',
                'latitude' => -6.379578,
                'longitude' => 106.846475,
                'catatan' => 'Pembersihan enclosure lama dan resplicing tray 1 sambungan kabel jumper baru.',
                'created_by' => $teknis2->id,
            ]
        );

        $jcCoresData2 = [
            ['tube_asal' => 'Tube 1 (Biru)', 'core_asal' => 'Core 1 (Biru)', 'tube_tujuan' => 'Tube 1 (Biru)', 'core_tujuan' => 'Core 1 (Biru)', 'status_core' => 'TERHUBUNG', 'loss_db' => 0.02, 'keterangan' => 'Splicing ke kabel arah SW Reog'],
            ['tube_asal' => 'Tube 1 (Biru)', 'core_asal' => 'Core 2 (Oranye)', 'tube_tujuan' => 'Tube 1 (Biru)', 'core_tujuan' => 'Core 2 (Oranye)', 'status_core' => 'TERHUBUNG', 'loss_db' => 0.03, 'keterangan' => 'Splicing ke kabel arah SW Reog'],
            ['tube_asal' => 'Tube 1 (Biru)', 'core_asal' => 'Core 3 (Hijau)', 'tube_tujuan' => 'Tube 1 (Biru)', 'core_tujuan' => 'Core 3 (Hijau)', 'status_core' => 'TERHUBUNG', 'loss_db' => 0.02, 'keterangan' => 'Splicing ke kabel arah SW Reog'],
            ['tube_asal' => 'Tube 1 (Biru)', 'core_asal' => 'Core 4 (Coklat)', 'tube_tujuan' => 'Tube 1 (Biru)', 'core_tujuan' => 'Core 4 (Coklat)', 'status_core' => 'TERHUBUNG', 'loss_db' => 0.02, 'keterangan' => 'Splicing ke kabel arah SW Reog'],
        ];
        foreach ($jcCoresData2 as $cd2) {
            TiketJointClosureCore::updateOrCreate(
                ['id_joint_closure' => $jc2->id, 'tube_asal' => $cd2['tube_asal'], 'core_asal' => $cd2['core_asal']],
                $cd2
            );
        }

        // Dokumentasi Tiket 1
        $docs1 = [
            ['kategori' => 'KONDISI_AWAL', 'file_path' => 'dokumentasi/dummy_putus.jpg', 'timestamp' => Carbon::create(2026, 9, 14, 22, 35, 0), 'latitude' => -6.379199, 'longitude' => 106.846552],
            ['kategori' => 'OTDR_TRACE', 'file_path' => 'dokumentasi/dummy_otdr.jpg', 'timestamp' => Carbon::create(2026, 9, 14, 21, 50, 0), 'latitude' => -6.918900, 'longitude' => 107.615200],
            ['kategori' => 'PROSES_REPAIR', 'file_path' => 'dokumentasi/dummy_splicing.jpg', 'timestamp' => Carbon::create(2026, 9, 15, 0, 45, 0), 'latitude' => -6.379199, 'longitude' => 106.846552],
            ['kategori' => 'CLOSURE_INSTALLED', 'file_path' => 'dokumentasi/dummy_closure.jpg', 'timestamp' => Carbon::create(2026, 9, 15, 2, 20, 0), 'latitude' => -6.379578, 'longitude' => 106.846475],
            ['kategori' => 'HASIL_AKHIR', 'file_path' => 'dokumentasi/dummy_finish.jpg', 'timestamp' => Carbon::create(2026, 9, 15, 2, 40, 0), 'latitude' => -6.379578, 'longitude' => 106.846475],
        ];
        foreach ($docs1 as $d) {
            Dokumentasi::updateOrCreate(['id_tiket' => $tiket1->id, 'kategori' => $d['kategori']], $d);
        }

        // Stop Clock Tiket 1 (Pernah dijeda 30 menit karena hujan petir)
        TiketStopClock::updateOrCreate(
            ['id_tiket' => $tiket1->id, 'alasan_kategori' => 'CUACA_BURUK'],
            [
                'start_time' => Carbon::create(2026, 9, 14, 23, 30, 0),
                'end_time' => Carbon::create(2026, 9, 15, 0, 0, 0),
                'duration_minutes' => 30,
                'alasan_detail' => 'Hujan lebat disertai angin kencang dan petir di lokasi penarikan kabel span tiang.',
                'requested_by' => $teknis1->id,
                'stopped_by' => $helpdesk->id,
                'is_active' => false,
            ]
        );

        // Handover Shift Tiket 1
        TiketHandoverShift::updateOrCreate(
            ['id_tiket' => $tiket1->id, 'shift_from' => 'Shift Malam (20:00 - 04:00)'],
            [
                'shift_to' => 'Shift Pagi (04:00 - 12:00)',
                'user_from_id' => $teknis1->id,
                'user_to_id' => $teknis2->id,
                'catatan_handover' => 'Pekerjaan splicing dan link UP telah selesai 100%. Tiket siap diajukan closing awal dan verifikasi helpdesk.',
            ]
        );


        // ═════════════════════════════════════════════════════════════════════
        // 2. TIKET #2: PROSES / MANUVER CORE (MENAMPILKAN CONTOH MODUL MANUVER)
        // ═════════════════════════════════════════════════════════════════════
        $tiket2 = Tiket::updateOrCreate(
            ['no_tiket' => 'BDG-20260918-003'],
            [
                'status_link_impact' => 'Backbone : SW BBLU - SW Reog Secondary Path Bending',
                'backbone_segment' => 'SW BBLU - SW Reog',
                'deskripsi' => 'Jalur proteksi redaman melonjak ke -28dB setelah badai. Dilakukan pengalihan / swapping core cadangan.',
                'tipe_penanganan' => 'MANUVER_CORE',
                'tanggal_open' => Carbon::create(2026, 9, 18, 9, 30, 0),
                'first_response_at' => Carbon::create(2026, 9, 18, 9, 45, 0),
                'response_time_minutes' => 15,
                'tanggal_close' => null,
                'resolved_at' => null,
                'status' => 'PROSES',
                'sla_target_minutes' => 360,
                'mttr_minutes' => null,
                'total_stop_clock_minutes' => 0,
                'is_stop_clock' => false,
                'sla_status' => 'NA',
                'created_by' => $helpdesk->id,
                'resolved_by' => null,
                'closed_by' => null,
            ]
        );

        // Kronologis Tiket 2
        $kronologisList2 = [
            [
                'id_tiket' => $tiket2->id,
                'timestamp' => Carbon::create(2026, 9, 18, 9, 45, 0),
                'user_id' => $teknis1->id,
                'kategori' => 'IZIN',
                'informasi' => 'Tim teknis tiba di OTB SW BBLU untuk mapping swapping core proteksi.',
                'latitude' => -6.914744,
                'longitude' => 107.609810,
            ],
            [
                'id_tiket' => $tiket2->id,
                'timestamp' => Carbon::create(2026, 9, 18, 10, 30, 0),
                'user_id' => $teknis1->id,
                'kategori' => 'OTDR',
                'informasi' => 'Core 1 & 2 pada Tube 2 mengalami bending di KM 1.8. Diputuskan manuver ke Tube 2 Core 7 & 8 yang spare normal.',
                'latitude' => -6.918900,
                'longitude' => 107.615200,
            ],
            [
                'id_tiket' => $tiket2->id,
                'timestamp' => Carbon::create(2026, 9, 18, 11, 15, 0),
                'user_id' => $teknis2->id,
                'kategori' => 'JOINTING',
                'informasi' => 'Patching jumper patchcord di OTB BBLU dan OTB Reog selesai diselaraskan.',
                'latitude' => -6.920000,
                'longitude' => 107.620000,
            ],
        ];
        foreach ($kronologisList2 as $k2) {
            Kronologis::updateOrCreate(
                ['id_tiket' => $k2['id_tiket'], 'timestamp' => $k2['timestamp']],
                $k2
            );
        }

        // Resume Tiket 2
        ResumePekerjaan::updateOrCreate(
            ['id_tiket' => $tiket2->id],
            [
                'tipe_penanganan' => 'MANUVER_CORE',
                'joint_closure_type' => 'OTB Rack 24 Port',
                'core_count_jointed' => 4,
                'team_om' => ['Rian Suryana (Lead)', 'Agus Hermawan (NOC Tech)'],
                'problem_temuan' => 'Microbending pada Tube 2 Core 1 & Core 2 segmen BBLU-Reog.',
                'action' => 'Manuver alokasi traffic utama ke Tube 2 Core 7 & Core 8 (Spare) pada ODF BBLU dan ODF Reog.',
                'catatan_tambahan' => 'Jalur utama kembali prima dengan redaman -17.8 dB.',
            ]
        );

        // Manuver Core Records (SEBELUM dan SESUDAH)
        $manuverRecords = [
            // Titik 1: OTB SW BBLU
            [
                'id_tiket' => $tiket2->id,
                'lokasi_tipe' => 'OTB',
                'titik' => 'OTB SW BBLU Rack-01',
                'core_asal' => 'Port 01 (Tube 2 Core 1)',
                'core_tujuan' => 'Port 01 (Tube 2 Core 1 - Bending -28dB)',
                'core_dialihkan' => 'Port 07 (Tube 2 Core 7)',
                'titik_kembali' => 'OTB SW Reog Port 07',
                'tipe' => 'SEBELUM',
                'status_manuver' => 'TEMPORARY',
                'status_core_aset' => 'BROKEN_LOSS',
                'keterangan' => 'Alokasi eksisting sebelum dialihkan karena high loss',
            ],
            [
                'id_tiket' => $tiket2->id,
                'lokasi_tipe' => 'OTB',
                'titik' => 'OTB SW BBLU Rack-01',
                'core_asal' => 'Port 01 (Traffic Utama)',
                'core_tujuan' => 'Port 07 (Tube 2 Core 7 - Normal -17.8dB)',
                'core_dialihkan' => 'Port 07 (Tube 2 Core 7)',
                'titik_kembali' => 'OTB SW Reog Port 07',
                'tipe' => 'SESUDAH',
                'status_manuver' => 'TEMPORARY',
                'status_core_aset' => 'OCCUPIED_MANUVER',
                'keterangan' => 'Hasil manuver ke spare core 7 - Traffic UP Normal',
            ],
            // Titik 2: JC-04 Reog
            [
                'id_tiket' => $tiket2->id,
                'lokasi_tipe' => 'CLOSURE_LAPANGAN',
                'titik' => 'JC-04 Reog (Tiang #12)',
                'core_asal' => 'Tube 2 Core 2',
                'core_tujuan' => 'Tube 2 Core 2',
                'core_dialihkan' => 'Tube 2 Core 8',
                'titik_kembali' => 'ODF Reog',
                'tipe' => 'SEBELUM',
                'status_manuver' => 'TEMPORARY',
                'status_core_aset' => 'BROKEN_LOSS',
                'keterangan' => 'Core 2 proteksi putus di dalam tray lama',
            ],
            [
                'id_tiket' => $tiket2->id,
                'lokasi_tipe' => 'CLOSURE_LAPANGAN',
                'titik' => 'JC-04 Reog (Tiang #12)',
                'core_asal' => 'Tube 2 Core 2 (Proteksi)',
                'core_tujuan' => 'Tube 2 Core 8 (Spare Aktif)',
                'core_dialihkan' => 'Tube 2 Core 8',
                'titik_kembali' => 'ODF Reog',
                'tipe' => 'SESUDAH',
                'status_manuver' => 'TEMPORARY',
                'status_core_aset' => 'OCCUPIED_MANUVER',
                'keterangan' => 'Swapping berhasil, redaman normal -18.0 dB',
            ],
        ];

        foreach ($manuverRecords as $mr) {
            ManuverCore::updateOrCreate(
                ['id_tiket' => $mr['id_tiket'], 'titik' => $mr['titik'], 'tipe' => $mr['tipe'], 'core_asal' => $mr['core_asal']],
                $mr
            );
        }

        // Material Tiket 2
        Material::updateOrCreate(
            ['id_tiket' => $tiket2->id, 'nama_material' => 'Patchcord Fiber Optic LC-SC Duplex 3M'],
            ['jumlah' => 4, 'satuan' => 'pcs']
        );
        Material::updateOrCreate(
            ['id_tiket' => $tiket2->id, 'nama_material' => 'Protection Sleeve 45mm'],
            ['jumlah' => 8, 'satuan' => 'pcs']
        );

        // Titik Perbaikan Tiket 2
        TitikPerbaikan::updateOrCreate(
            ['id_tiket' => $tiket2->id, 'nama_titik' => 'POP SW BBLU (ODF Main)'],
            ['latitude' => -6.914744, 'longitude' => 107.609810, 'keterangan' => 'Ruang Server Lantai 2']
        );
        TitikPerbaikan::updateOrCreate(
            ['id_tiket' => $tiket2->id, 'nama_titik' => 'JC-04 Reog KM 1.8'],
            ['latitude' => -6.918900, 'longitude' => 107.615200, 'keterangan' => 'Tiang No 12 Depan Bank']
        );

        // Dokumentasi Tiket 2
        Dokumentasi::updateOrCreate(
            ['id_tiket' => $tiket2->id, 'kategori' => 'OTDR_TRACE'],
            ['file_path' => 'dokumentasi/dummy_otdr_manuver.jpg', 'timestamp' => Carbon::create(2026, 9, 18, 10, 35, 0), 'latitude' => -6.918900, 'longitude' => 107.615200]
        );
        Dokumentasi::updateOrCreate(
            ['id_tiket' => $tiket2->id, 'kategori' => 'PROSES_REPAIR'],
            ['file_path' => 'dokumentasi/dummy_otb_patching.jpg', 'timestamp' => Carbon::create(2026, 9, 18, 11, 20, 0), 'latitude' => -6.914744, 'longitude' => 107.609810]
        );


        // ═════════════════════════════════════════════════════════════════════
        // 3. TIKET TAMBAHAN (OPEN, PROSES, CLOSE DENGAN VARIASI LAIN)
        // ═════════════════════════════════════════════════════════════════════
        $otherTickets = [
            [
                'no_tiket' => 'BDG-20260915-001',
                'status_link_impact' => 'Backbone : SW Cikutra - SW Dago High Latency & Packet Loss',
                'backbone_segment' => 'SW Cikutra - SW Dago',
                'deskripsi' => 'Bending terdeteksi di KM 4.2. Redaman melonjak drastis hingga -28 dB.',
                'tipe_penanganan' => 'JOINTING_LURUS',
                'tanggal_open' => Carbon::create(2026, 9, 15, 8, 30, 0),
                'tanggal_close' => Carbon::create(2026, 9, 15, 16, 0, 0),
                'status' => 'CLOSE',
                'mttr_minutes' => 450,
                'sla_target_minutes' => 360,
                'sla_status' => 'LEBIH',
                'created_by' => $helpdesk->id,
                'closed_by' => $admin->id,
            ],
            [
                'no_tiket' => 'BDG-20260916-001',
                'status_link_impact' => 'Segment : SW Pasteur - SW Sukajadi FO Cut',
                'backbone_segment' => 'SW Pasteur - SW Sukajadi',
                'deskripsi' => 'Pekerjaan galian drainase kota mengenai kabel fiber optic 24 core.',
                'tipe_penanganan' => 'JOINTING_LURUS',
                'tanggal_open' => Carbon::create(2026, 9, 16, 10, 15, 0),
                'tanggal_close' => Carbon::create(2026, 9, 16, 13, 30, 0),
                'status' => 'CLOSE',
                'mttr_minutes' => 195,
                'sla_target_minutes' => 240,
                'sla_status' => 'TEPAT',
                'created_by' => $helpdesk->id,
                'closed_by' => $helpdesk->id,
            ],
            [
                'no_tiket' => 'BDG-20260917-001',
                'status_link_impact' => 'Segment : SW Kopo - SW Cibaduyut Core Degradation',
                'backbone_segment' => 'SW Kopo - SW Cibaduyut',
                'deskripsi' => 'Redaman tinggi di closure lama dekat jembatan tol.',
                'tipe_penanganan' => 'JOINTING_LURUS',
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
                'no_tiket' => 'BDG-20260918-001',
                'status_link_impact' => 'Backbone : SW Cimahi - SW Padalarang Cut KM 11',
                'backbone_segment' => 'SW Cimahi - SW Padalarang',
                'deskripsi' => 'Kabel 48 core putus tersangkut dahan pohon tumbang saat hujan lebat.',
                'tipe_penanganan' => 'JOINTING_LURUS',
                'tanggal_open' => Carbon::create(2026, 9, 18, 6, 15, 0),
                'tanggal_close' => null,
                'status' => 'OPEN',
                'mttr_minutes' => null,
                'sla_target_minutes' => 240,
                'sla_status' => 'NA',
                'created_by' => $helpdesk->id,
                'closed_by' => null,
            ],
        ];

        foreach ($otherTickets as $ot) {
            Tiket::updateOrCreate(['no_tiket' => $ot['no_tiket']], $ot);
        }
    }
}
