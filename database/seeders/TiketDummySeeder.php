<?php

namespace Database\Seeders;

use App\Models\Dokumentasi;
use App\Models\Kronologis;
use App\Models\ManuverCore;
use App\Models\Material;
use App\Models\NotifikasiLog;
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
use Illuminate\Support\Facades\DB;

class TiketDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengisi semua fitur sistem:
     * - Tiket (OPEN, PROSES, CLOSE, SLA TEPAT, SLA LEBIH)
     * - Kronologis (Timeline WhatsApp Style dengan Kategori & Geo-Koord)
     * - Resume Pekerjaan & Tim OM
     * - Material Terpakai
     * - Titik Perbaikan (Peta Leaflet Koordinat)
     * - Stop Clock SLA (Selesai & Aktif)
     * - Handover Shift Teknisi
     * - Joint Closure & Splicing Core (Tube & Core Color Codes + Loss dB)
     * - Manuver Core (Sebelum vs Sesudah untuk Visual Fiber Patcher)
     * - Dokumentasi Foto Lapangan
     */
    public function run(): void
    {
        // 1. Ambil referensi user berdasarkan role
        $admin = User::where('role', 'admin')->first() ?? User::first();
        $helpdesk = User::where('role', 'helpdesk')->first() ?? $admin;
        $teknis1 = User::where('role', 'teknis')->first() ?? $admin;
        $teknis2 = User::where('role', 'teknis')->skip(1)->first() ?? $teknis1;

        if (!$admin || !$helpdesk || !$teknis1) {
            $this->command->warn('Pastikan UserSeeder telah dijalankan terlebih dahulu.');
            return;
        }

        // =========================================================================
        // TIKET 1: CLOSED - JOINTING LURUS & JOINT CLOSURE (SLA TEPAT)
        // =========================================================================
        $openTime1 = Carbon::now()->subDays(2)->setHour(21)->setMinute(0)->setSecond(0);
        $firstResp1 = (clone $openTime1)->addMinutes(15);
        $resolved1 = (clone $openTime1)->addHours(5)->addMinutes(20);
        $closeTime1 = (clone $openTime1)->addHours(5)->addMinutes(45);

        $tiket1 = Tiket::updateOrCreate(
            ['no_tiket' => 'BDG-20260920-001'],
            [
                'status_link_impact' => 'Backbone : SW BBLU - SW Reog Down',
                'backbone_segment' => 'SW BBLU - SW Reog',
                'deskripsi' => 'LOS total pada link backbone BBLU arah Reog. Indikasi kabel FO 24 Core putus akibat tersangkut truk kontainer di crossingan jalan raya.',
                'closing_notes_teknisi' => 'Penyambungan jointing 2 titik closure baru (JC1 & JC2) selesai. Semua 24 core link backbone UP normal dengan redaman optik rata-rata -18.2 dB.',
                'tipe_penanganan' => 'JOINTING_LURUS',
                'tanggal_open' => $openTime1,
                'first_response_at' => $firstResp1,
                'response_time_minutes' => 15,
                'resolved_at' => $resolved1,
                'tanggal_close' => $closeTime1,
                'status' => 'CLOSE',
                'sla_target_minutes' => 360,
                'mttr_minutes' => 315,
                'total_stop_clock_minutes' => 30,
                'is_stop_clock' => false,
                'sla_status' => 'TEPAT',
                'created_by' => $helpdesk->id,
                'resolved_by' => $teknis1->id,
                'closed_by' => $helpdesk->id,
            ]
        );

        // Kronologis Tiket 1
        $kronologisData1 = [
            ['offset' => 15, 'user' => $teknis1, 'kat' => 'IZIN', 'info' => 'Tim teknis izin bergerak menuju lokasi insiden segment SW BBLU - SW Reog.', 'lat' => -6.914744, 'lng' => 107.609810],
            ['offset' => 45, 'user' => $teknis1, 'kat' => 'OTDR', 'info' => 'Hasil ukur OTDR dari OTB SW BBLU mendeteksi event putus total pada jarak 4.8 KM arah Reog.', 'lat' => -6.918900, 'lng' => 107.615200],
            ['offset' => 90, 'user' => $teknis2, 'kat' => 'TRACING', 'info' => 'Titik fisik kabel putus ditemukan di perempatan jalan raya, kabel ditarik truk tronton kontainer.', 'lat' => -6.379199, 'lng' => 106.846552],
            ['offset' => 130, 'user' => $teknis1, 'kat' => 'MATERIAL', 'info' => 'Material kabel jumper 24 core 150m dan 2 unit Joint Closure Dome tiba di lokasi perbaikan.', 'lat' => -6.379199, 'lng' => 106.846552],
            ['offset' => 210, 'user' => $teknis1, 'kat' => 'JOINTING', 'info' => 'Splicing closure 1 (JC-BBLU-01) selesai 24 core, lanjut penarikan kabel span ke JC-BBLU-02.', 'lat' => -6.379199, 'lng' => 106.846552],
            ['offset' => 285, 'user' => $teknis2, 'kat' => 'JOINTING', 'info' => 'Splicing closure 2 (JC-BBLU-02) rampung. Seluruh core 1-24 tersambung dengan rata-rata loss 0.03 dB.', 'lat' => -6.379578, 'lng' => 106.846475],
            ['offset' => 315, 'user' => $teknis1, 'kat' => 'LINK_UP', 'info' => 'Link backbone BBLU - Reog terverifikasi UP normal. Trafik stabil, redaman rata-rata -18.2 dB.', 'lat' => -6.379578, 'lng' => 106.846475],
            ['offset' => 335, 'user' => $teknis2, 'kat' => 'SELESAI', 'info' => 'Pemasangan closure ke tiang selesai, perapihan kabel span, dan pembersihan area kerja.', 'lat' => -6.379578, 'lng' => 106.846475],
        ];

        foreach ($kronologisData1 as $k) {
            Kronologis::create([
                'id_tiket' => $tiket1->id,
                'timestamp' => (clone $openTime1)->addMinutes($k['offset']),
                'user_id' => $k['user']->id,
                'kategori' => $k['kat'],
                'informasi' => $k['info'],
                'latitude' => $k['lat'],
                'longitude' => $k['lng'],
            ]);
        }

        // Stop Clock Tiket 1 (Selesai karena hujan deras)
        TiketStopClock::create([
            'id_tiket' => $tiket1->id,
            'start_time' => (clone $openTime1)->addMinutes(140),
            'end_time' => (clone $openTime1)->addMinutes(170),
            'duration_minutes' => 30,
            'alasan_kategori' => 'CUACA_BURUK',
            'alasan_detail' => 'Hujan badai disertai petir lebat di lokasi span tiang, proses penarikan kabel ditunda sementara demi K3 keselamatan teknisi.',
            'requested_by' => $teknis1->id,
            'stopped_by' => $helpdesk->id,
            'is_active' => false,
        ]);

        // Handover Shift Tiket 1
        TiketHandoverShift::create([
            'id_tiket' => $tiket1->id,
            'shift_from' => 'Shift Malam (20:00 - 04:00)',
            'shift_to' => 'Shift Pagi (04:00 - 12:00)',
            'user_from_id' => $teknis1->id,
            'user_to_id' => $teknis2->id,
            'catatan_handover' => 'Pekerjaan jointing 2 titik closure telah tuntas dan link UP normal. Mohon dipantau kestabilan link selama masa observasi 2 jam.',
        ]);

        // Resume Pekerjaan Tiket 1
        ResumePekerjaan::updateOrCreate(
            ['id_tiket' => $tiket1->id],
            [
                'tipe_penanganan' => 'JOINTING_LURUS',
                'joint_closure_type' => 'DOME 24-48 Core',
                'core_count_jointed' => 24,
                'team_om' => json_encode(['Rian Suryana (Lead)', 'Budi Santoso (Splicer)', 'Dedi Supriadi (Lineman)']),
                'problem_temuan' => 'Kabel FO Aerial 24 Core terputus akibat tersangkut truk box kontainer tinggi di perempatan jalan raya crossing utama.',
                'action' => 'Penarikan jumper kabel FO 24 core sepanjang 150 meter, pemasangan 2 titik Joint Closure baru (JC1 & JC2), serta rekoneksi 24 core.',
                'catatan_tambahan' => 'Rekomendasi penambahan tiang peninggi kabel span crossing agar memenuhi batas standar keselamatan ketinggian jalan raya (minimal 6 meter).',
            ]
        );

        // Material Tiket 1
        $materials1 = [
            ['nama' => 'Joint Closure Dome 24 Core', 'jumlah' => 2, 'satuan' => 'Unit'],
            ['nama' => 'Kabel FO ADSS 24 Core', 'jumlah' => 150, 'satuan' => 'Meter'],
            ['nama' => 'Protection Sleeve 60mm', 'jumlah' => 48, 'satuan' => 'Pcs'],
            ['nama' => 'Suspension Clamp FO', 'jumlah' => 4, 'satuan' => 'Set'],
            ['nama' => 'Dead End Clamp / Tension Clamp', 'jumlah' => 4, 'satuan' => 'Set'],
        ];
        foreach ($materials1 as $mat) {
            Material::create([
                'id_tiket' => $tiket1->id,
                'nama_material' => $mat['nama'],
                'jumlah' => $mat['jumlah'],
                'satuan' => $mat['satuan'],
            ]);
        }

        // Titik Perbaikan Tiket 1
        TitikPerbaikan::create([
            'id_tiket' => $tiket1->id,
            'nama_titik' => 'JC1 (Joint Closure Span Barat)',
            'latitude' => -6.379199,
            'longitude' => 106.846552,
            'keterangan' => 'Tiang No 18 depan ruko - Posisi Closure Baru Arah BBLU',
        ]);
        TitikPerbaikan::create([
            'id_tiket' => $tiket1->id,
            'nama_titik' => 'JC2 (Joint Closure Span Timur)',
            'latitude' => -6.379578,
            'longitude' => 106.846475,
            'keterangan' => 'Tiang No 22 seberang SPBU - Posisi Closure Baru Arah Reog',
        ]);

        // Joint Closure Tiket 1 - JC1 (ASET BARU)
        $jc1 = TiketJointClosure::create([
            'id_tiket' => $tiket1->id,
            'nama_closure' => 'JC-BBLU-01 (Crossing Barat)',
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
            'catatan' => 'Pemasangan closure baru akibat pemotongan span putus. Tray 1 & Tray 2 digunakan untuk alokasi backbone utama.',
            'created_by' => $teknis1->id,
        ]);

        // Joint Closure Cores JC1
        $coresJc1 = [
            ['tube_a' => 'Tube 1 (Biru)', 'core_a' => 'Core 1 (Biru)', 'tube_b' => 'Tube 1 (Biru)', 'core_b' => 'Core 1 (Biru)', 'status' => 'TERHUBUNG', 'loss' => 0.02, 'ket' => 'Backbone Primary Link A'],
            ['tube_a' => 'Tube 1 (Biru)', 'core_a' => 'Core 2 (Oranye)', 'tube_b' => 'Tube 1 (Biru)', 'core_b' => 'Core 2 (Oranye)', 'status' => 'TERHUBUNG', 'loss' => 0.03, 'ket' => 'Backbone Primary Link B'],
            ['tube_a' => 'Tube 1 (Biru)', 'core_a' => 'Core 3 (Hijau)', 'tube_b' => 'Tube 1 (Biru)', 'core_b' => 'Core 3 (Hijau)', 'status' => 'TERHUBUNG', 'loss' => 0.01, 'ket' => 'Metro-E Enterprise Link 1'],
            ['tube_a' => 'Tube 1 (Biru)', 'core_a' => 'Core 4 (Coklat)', 'tube_b' => 'Tube 1 (Biru)', 'core_b' => 'Core 4 (Coklat)', 'status' => 'TERHUBUNG', 'loss' => 0.04, 'ket' => 'Metro-E Enterprise Link 2'],
            ['tube_a' => 'Tube 1 (Biru)', 'core_a' => 'Core 5 (Abu-abu)', 'tube_b' => 'Tube 1 (Biru)', 'core_b' => 'Core 5 (Abu-abu)', 'status' => 'TERHUBUNG', 'loss' => 0.02, 'ket' => 'OLT Distribusi Timur'],
            ['tube_a' => 'Tube 1 (Biru)', 'core_a' => 'Core 6 (Putih)', 'tube_b' => 'Tube 1 (Biru)', 'core_b' => 'Core 6 (Putih)', 'status' => 'TERHUBUNG', 'loss' => 0.02, 'ket' => 'OLT Distribusi Barat'],
            ['tube_a' => 'Tube 2 (Oranye)', 'core_a' => 'Core 1 (Biru)', 'tube_b' => null, 'core_b' => null, 'status' => 'SPARE', 'loss' => null, 'ket' => 'Cadangan Tray 2 - Alokasi Spare'],
            ['tube_a' => 'Tube 2 (Oranye)', 'core_a' => 'Core 2 (Oranye)', 'tube_b' => null, 'core_b' => null, 'status' => 'SPARE', 'loss' => null, 'ket' => 'Cadangan Tray 2 - Alokasi Spare'],
            ['tube_a' => 'Tube 2 (Oranye)', 'core_a' => 'Core 3 (Hijau)', 'tube_b' => 'Tube 2 (Oranye)', 'core_b' => 'Core 7 (Merah)', 'status' => 'MANUVER', 'loss' => 0.05, 'ket' => 'Swapping alokasi ke port proteksi'],
            ['tube_a' => 'Tube 3 (Hijau)', 'core_a' => 'Core 12 (Toska)', 'tube_b' => null, 'core_b' => null, 'status' => 'LOSS_PUTUS', 'loss' => 12.50, 'ket' => 'Core mengalami microbending bawaan kabel lama'],
        ];

        foreach ($coresJc1 as $cj) {
            TiketJointClosureCore::create([
                'id_joint_closure' => $jc1->id,
                'tube_asal' => $cj['tube_a'],
                'core_asal' => $cj['core_a'],
                'tube_tujuan' => $cj['tube_b'],
                'core_tujuan' => $cj['core_b'],
                'status_core' => $cj['status'],
                'loss_db' => $cj['loss'],
                'keterangan' => $cj['ket'],
            ]);
        }

        // Joint Closure Tiket 1 - JC2 (EKSISTING)
        $jc2 = TiketJointClosure::create([
            'id_tiket' => $tiket1->id,
            'nama_closure' => 'JC-BBLU-02 (Crossing Timur)',
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
        ]);

        foreach (range(1, 4) as $idx) {
            $colors = ['Biru', 'Oranye', 'Hijau', 'Coklat'];
            $colName = $colors[$idx - 1];
            TiketJointClosureCore::create([
                'id_joint_closure' => $jc2->id,
                'tube_asal' => 'Tube 1 (Biru)',
                'core_asal' => "Core {$idx} ({$colName})",
                'tube_tujuan' => 'Tube 1 (Biru)',
                'core_tujuan' => "Core {$idx} ({$colName})",
                'status_core' => 'TERHUBUNG',
                'loss_db' => 0.02,
                'keterangan' => 'Splicing ke kabel arah SW Reog',
            ]);
        }

        // Dokumentasi Tiket 1
        $docs1 = [
            ['kat' => 'KONDISI_AWAL', 'path' => 'dokumentasi/dummy_putus.jpg', 'offset' => 45, 'lat' => -6.379199, 'lng' => 106.846552],
            ['kat' => 'OTDR_TRACE', 'path' => 'dokumentasi/dummy_otdr.jpg', 'offset' => 50, 'lat' => -6.918900, 'lng' => 107.615200],
            ['kat' => 'PROSES_REPAIR', 'path' => 'dokumentasi/dummy_splicing.jpg', 'offset' => 210, 'lat' => -6.379199, 'lng' => 106.846552],
            ['kat' => 'HASIL_AKHIR', 'path' => 'dokumentasi/dummy_finish.jpg', 'offset' => 335, 'lat' => -6.379578, 'lng' => 106.846475],
        ];
        foreach ($docs1 as $d) {
            Dokumentasi::create([
                'id_tiket' => $tiket1->id,
                'kategori' => $d['kat'],
                'file_path' => $d['path'],
                'timestamp' => (clone $openTime1)->addMinutes($d['offset']),
                'latitude' => $d['lat'],
                'longitude' => $d['lng'],
            ]);
        }


        // =========================================================================
        // TIKET 2: IN-PROGRESS (PROSES) - MANUVER CORE & FIBER PATCHER + STOP CLOCK AKTIF
        // =========================================================================
        $openTime2 = Carbon::now()->subHours(4);
        $firstResp2 = (clone $openTime2)->addMinutes(12);

        $tiket2 = Tiket::updateOrCreate(
            ['no_tiket' => 'BDG-20260924-002'],
            [
                'status_link_impact' => 'Backbone : SW Cikutra - SW Dago High Loss (-29dB)',
                'backbone_segment' => 'SW Cikutra - SW Dago',
                'deskripsi' => 'Kenaikan redaman drastis pada link proteksi Cikutra-Dago. Terjadi penurunan performa throughput. Dilakukan pengalihan / swapping port core spare.',
                'closing_notes_teknisi' => null,
                'tipe_penanganan' => 'MANUVER_CORE',
                'tanggal_open' => $openTime2,
                'first_response_at' => $firstResp2,
                'response_time_minutes' => 12,
                'resolved_at' => null,
                'tanggal_close' => null,
                'status' => 'PROSES',
                'sla_target_minutes' => 360,
                'mttr_minutes' => null,
                'total_stop_clock_minutes' => 45,
                'is_stop_clock' => true,
                'sla_status' => 'NA',
                'created_by' => $helpdesk->id,
                'resolved_by' => null,
                'closed_by' => null,
            ]
        );

        // Kronologis Tiket 2
        $kronologisData2 = [
            ['offset' => 12, 'user' => $teknis1, 'kat' => 'IZIN', 'info' => 'Tim teknis menerima eskalasi dan tiba di OTB POP SW Cikutra untuk investigasi link.', 'lat' => -6.895000, 'lng' => 107.632000],
            ['offset' => 45, 'user' => $teknis1, 'kat' => 'OTDR', 'info' => 'Core 1 & 2 pada Tube 2 mengalami microbending di KM 2.3. Diputuskan manuver alokasi traffic ke Tube 2 Core 7 & 8 (Spare Aktif).', 'lat' => -6.892000, 'lng' => 107.628000],
            ['offset' => 90, 'user' => $teknis2, 'kat' => 'JOINTING', 'info' => 'Patching jumper patchcord di OTB Rack Cikutra dan OTB Dago selesai diselaraskan.', 'lat' => -6.885000, 'lng' => 107.615000],
            ['offset' => 135, 'user' => $teknis1, 'kat' => 'LAIN', 'info' => 'Menunggu izin akses gedung pengelola POP Dago untuk verifikasi redaman akhir pada power meter OLT.', 'lat' => -6.885000, 'lng' => 107.615000],
        ];

        foreach ($kronologisData2 as $k) {
            Kronologis::create([
                'id_tiket' => $tiket2->id,
                'timestamp' => (clone $openTime2)->addMinutes($k['offset']),
                'user_id' => $k['user']->id,
                'kategori' => $k['kat'],
                'informasi' => $k['info'],
                'latitude' => $k['lat'],
                'longitude' => $k['lng'],
            ]);
        }

        // Stop Clock Aktif Tiket 2 (Akses Lokasi Gedung Pengelola)
        TiketStopClock::create([
            'id_tiket' => $tiket2->id,
            'start_time' => (clone $openTime2)->addMinutes(135),
            'end_time' => null,
            'duration_minutes' => 45,
            'alasan_kategori' => 'AKSES_LOKASI',
            'alasan_detail' => 'Menunggu verifikasi kartu izin masuk gedung server POP Dago oleh pihak security pengelola kawasan.',
            'requested_by' => $teknis1->id,
            'stopped_by' => null,
            'is_active' => true,
        ]);

        // Handover Shift Tiket 2
        TiketHandoverShift::create([
            'id_tiket' => $tiket2->id,
            'shift_from' => 'Shift Pagi (08:00 - 16:00)',
            'shift_to' => 'Shift Sore (16:00 - 00:00)',
            'user_from_id' => $teknis1->id,
            'user_to_id' => $teknis2->id,
            'catatan_handover' => 'Manuver jumper patchcord di OTB Cikutra telah selesai. Tinggal menunggu approval akses POP Dago untuk cross-check power meter port 7.',
        ]);

        // Resume Pekerjaan Tiket 2
        ResumePekerjaan::updateOrCreate(
            ['id_tiket' => $tiket2->id],
            [
                'tipe_penanganan' => 'MANUVER_CORE',
                'joint_closure_type' => 'OTB Rack 24 Port & FAT Box',
                'core_count_jointed' => 4,
                'team_om' => json_encode(['Rian Suryana (Lead)', 'Agus Hermawan (NOC Tech)']),
                'problem_temuan' => 'Microbending pada Tube 2 Core 1 & Core 2 segmen Cikutra-Dago mengakibatkan redaman -29dB.',
                'action' => 'Manuver alokasi traffic utama ke Tube 2 Core 7 & Core 8 (Spare) pada ODF Cikutra dan ODF Dago.',
                'catatan_tambahan' => 'Jalur utama kembali prima dengan redaman normal -17.8 dB setelah manuver core.',
            ]
        );

        // Manuver Core Tiket 2 (SEBELUM vs SESUDAH untuk Visual Fiber Patcher)
        ManuverCore::create([
            'id_tiket' => $tiket2->id,
            'lokasi_tipe' => 'OTB',
            'titik' => 'OTB SW Cikutra Rack-01',
            'core_asal' => 'Port 01 (Tube 2 Core 1)',
            'core_tujuan' => 'Port 01 (Tube 2 Core 1 - Bending -29dB)',
            'core_dialihkan' => 'Port 07 (Tube 2 Core 7)',
            'titik_kembali' => 'OTB SW Dago Port 07',
            'tipe' => 'SEBELUM',
            'status_manuver' => 'TEMPORARY',
            'status_core_aset' => 'BROKEN_LOSS',
            'keterangan' => 'Alokasi eksisting sebelum dialihkan karena high loss',
        ]);

        ManuverCore::create([
            'id_tiket' => $tiket2->id,
            'lokasi_tipe' => 'OTB',
            'titik' => 'OTB SW Cikutra Rack-01',
            'core_asal' => 'Port 01 (Traffic Utama)',
            'core_tujuan' => 'Port 07 (Tube 2 Core 7 - Normal -17.8dB)',
            'core_dialihkan' => 'Port 07 (Tube 2 Core 7)',
            'titik_kembali' => 'OTB SW Dago Port 07',
            'tipe' => 'SESUDAH',
            'status_manuver' => 'TEMPORARY',
            'status_core_aset' => 'OCCUPIED_MANUVER',
            'keterangan' => 'Hasil manuver ke spare core 7 - Traffic UP Normal',
        ]);

        ManuverCore::create([
            'id_tiket' => $tiket2->id,
            'lokasi_tipe' => 'CLOSURE_LAPANGAN',
            'titik' => 'JC-03 Dago (Tiang #12)',
            'core_asal' => 'Tube 2 Core 2',
            'core_tujuan' => 'Tube 2 Core 2',
            'core_dialihkan' => 'Tube 2 Core 8',
            'titik_kembali' => 'ODF Dago',
            'tipe' => 'SEBELUM',
            'status_manuver' => 'TEMPORARY',
            'status_core_aset' => 'BROKEN_LOSS',
            'keterangan' => 'Core 2 proteksi putus di dalam tray lama',
        ]);

        ManuverCore::create([
            'id_tiket' => $tiket2->id,
            'lokasi_tipe' => 'CLOSURE_LAPANGAN',
            'titik' => 'JC-03 Dago (Tiang #12)',
            'core_asal' => 'Tube 2 Core 2 (Proteksi)',
            'core_tujuan' => 'Tube 2 Core 8 (Spare Aktif)',
            'core_dialihkan' => 'Tube 2 Core 8',
            'titik_kembali' => 'ODF Dago',
            'tipe' => 'SESUDAH',
            'status_manuver' => 'TEMPORARY',
            'status_core_aset' => 'OCCUPIED_MANUVER',
            'keterangan' => 'Swapping berhasil, redaman normal -18.0 dB',
        ]);

        // Material Tiket 2
        Material::create([
            'id_tiket' => $tiket2->id,
            'nama_material' => 'Patchcord Fiber Optic LC-SC Duplex 3M',
            'jumlah' => 4,
            'satuan' => 'Pcs',
        ]);
        Material::create([
            'id_tiket' => $tiket2->id,
            'nama_material' => 'Protection Sleeve 45mm',
            'jumlah' => 8,
            'satuan' => 'Pcs',
        ]);

        // Titik Perbaikan Tiket 2
        TitikPerbaikan::create([
            'id_tiket' => $tiket2->id,
            'nama_titik' => 'POP SW Cikutra (ODF Main)',
            'latitude' => -6.895000,
            'longitude' => 107.632000,
            'keterangan' => 'Ruang Server Lantai 2',
        ]);
        TitikPerbaikan::create([
            'id_tiket' => $tiket2->id,
            'nama_titik' => 'JC-03 Dago KM 2.3',
            'latitude' => -6.885000,
            'longitude' => 107.615000,
            'keterangan' => 'Tiang No 12 Depan Simpang Dago',
        ]);


        // =========================================================================
        // TIKET 3: OPEN - NEW TICKET (BARU DIKIRIM)
        // =========================================================================
        $openTime3 = Carbon::now()->subMinutes(35);

        $tiket3 = Tiket::updateOrCreate(
            ['no_tiket' => 'BDG-20260925-003'],
            [
                'status_link_impact' => 'Backbone : SW Soekarno Hatta - SW Buah Batu Alarm Major',
                'backbone_segment' => 'SW Soekarno Hatta - SW Buah Batu',
                'deskripsi' => 'Alarm major optical power receive drop di bawah ambang batas (-32 dBm). Indikasi bending atau tarikan kabel pada span jalur bypass Soekarno Hatta.',
                'closing_notes_teknisi' => null,
                'tipe_penanganan' => 'JOINTING_LURUS',
                'tanggal_open' => $openTime3,
                'first_response_at' => null,
                'response_time_minutes' => null,
                'resolved_at' => null,
                'tanggal_close' => null,
                'status' => 'OPEN',
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

        // Kronologis Tiket 3
        Kronologis::create([
            'id_tiket' => $tiket3->id,
            'timestamp' => $openTime3,
            'user_id' => $helpdesk->id,
            'kategori' => 'LAIN',
            'informasi' => 'Tiket gangguan dibuat oleh NOC HelpDesk berdasarkan notifikasi NMS Zabbix (Alarm Major Optical Loss).',
            'latitude' => -6.940000,
            'longitude' => 107.635000,
        ]);


        // =========================================================================
        // TIKET 4: PROSES - PERGANTIAN KABEL SPAN TIANG
        // =========================================================================
        $openTime4 = Carbon::now()->subHours(2)->subMinutes(15);
        $firstResp4 = (clone $openTime4)->addMinutes(10);

        $tiket4 = Tiket::updateOrCreate(
            ['no_tiket' => 'BDG-20260925-004'],
            [
                'status_link_impact' => 'Backbone : SW Pasteur - SW Sukajadi Down',
                'backbone_segment' => 'SW Pasteur - SW Sukajadi',
                'deskripsi' => 'Kabel FO 24C putus terbakar akibat percikan api trafo PLN di tiang simpang Pasteur.',
                'closing_notes_teknisi' => null,
                'tipe_penanganan' => 'PERGANTIAN_KABEL',
                'tanggal_open' => $openTime4,
                'first_response_at' => $firstResp4,
                'response_time_minutes' => 10,
                'resolved_at' => null,
                'tanggal_close' => null,
                'status' => 'PROSES',
                'sla_target_minutes' => 240,
                'mttr_minutes' => null,
                'total_stop_clock_minutes' => 0,
                'is_stop_clock' => false,
                'sla_status' => 'NA',
                'created_by' => $helpdesk->id,
                'resolved_by' => null,
                'closed_by' => null,
            ]
        );

        // Kronologis Tiket 4
        $kronologisData4 = [
            ['offset' => 10, 'user' => $teknis2, 'kat' => 'IZIN', 'info' => 'Tim teknis meluncur ke lokasi tiang trafo Simpang Pasteur.', 'lat' => -6.898000, 'lng' => 107.595000],
            ['offset' => 35, 'user' => $teknis2, 'kat' => 'TRACING', 'info' => 'Kabel FO hangus terbakar sepanjang 60 meter. Diperlukan span kabel pengganti 100m dan 2 closure.', 'lat' => -6.898000, 'lng' => 107.595000],
            ['offset' => 70, 'user' => $teknis1, 'kat' => 'MATERIAL', 'info' => 'Kabel FO ADSS 24C 100m, Closure Dome, dan klem tiang tiba di lokasi.', 'lat' => -6.898000, 'lng' => 107.595000],
            ['offset' => 110, 'user' => $teknis2, 'kat' => 'JOINTING', 'info' => 'Proses penarikan span baru selesai, teknisi sedang melakukan splicing di closure sisi barat.', 'lat' => -6.898000, 'lng' => 107.595000],
        ];

        foreach ($kronologisData4 as $k) {
            Kronologis::create([
                'id_tiket' => $tiket4->id,
                'timestamp' => (clone $openTime4)->addMinutes($k['offset']),
                'user_id' => $k['user']->id,
                'kategori' => $k['kat'],
                'informasi' => $k['info'],
                'latitude' => $k['lat'],
                'longitude' => $k['lng'],
            ]);
        }

        TitikPerbaikan::create([
            'id_tiket' => $tiket4->id,
            'nama_titik' => 'Tiang Trafo Simpang Pasteur',
            'latitude' => -6.898000,
            'longitude' => 107.595000,
            'keterangan' => 'Lokasi kabel terbakar dan penarikan span baru',
        ]);

        Material::create([
            'id_tiket' => $tiket4->id,
            'nama_material' => 'Kabel FO ADSS 24 Core',
            'jumlah' => 100,
            'satuan' => 'Meter',
        ]);
        Material::create([
            'id_tiket' => $tiket4->id,
            'nama_material' => 'Closure Dome 24 Core',
            'jumlah' => 2,
            'satuan' => 'Unit',
        ]);


        // =========================================================================
        // TIKET 5: CLOSED - OVER SLA (CONTOH SLA LEBIH UNTUK METRIK & KPI DASHBOARD)
        // =========================================================================
        $openTime5 = Carbon::now()->subDays(5)->setHour(10)->setMinute(0);
        $firstResp5 = (clone $openTime5)->addMinutes(20);
        $resolved5 = (clone $openTime5)->addHours(6)->addMinutes(20);
        $closeTime5 = (clone $openTime5)->addHours(6)->addMinutes(45);

        $tiket5 = Tiket::updateOrCreate(
            ['no_tiket' => 'BDG-20260918-005'],
            [
                'status_link_impact' => 'Backbone : SW Setiabudi - SW Lembang Cut',
                'backbone_segment' => 'SW Setiabudi - SW Lembang',
                'deskripsi' => 'Kabel tertimpa pohon tumbang di lereng pegunungan Lembang. Akses lokasi terhambat kemacetan wisata dan jalur tebing curam.',
                'closing_notes_teknisi' => 'Penyambungan darurat dengan kabel jumper 200m selesai. Link normal dengan redaman -19.5 dB.',
                'tipe_penanganan' => 'JOINTING_LURUS',
                'tanggal_open' => $openTime5,
                'first_response_at' => $firstResp5,
                'response_time_minutes' => 20,
                'resolved_at' => $resolved5,
                'tanggal_close' => $closeTime5,
                'status' => 'CLOSE',
                'sla_target_minutes' => 300, // 5 Jam
                'mttr_minutes' => 380,       // 6 Jam 20 Menit (Over SLA)
                'total_stop_clock_minutes' => 0,
                'is_stop_clock' => false,
                'sla_status' => 'LEBIH',
                'created_by' => $helpdesk->id,
                'resolved_by' => $teknis1->id,
                'closed_by' => $helpdesk->id,
            ]
        );

        // Kronologis Tiket 5
        Kronologis::create([
            'id_tiket' => $tiket5->id,
            'timestamp' => (clone $openTime5)->addMinutes(20),
            'user_id' => $teknis1->id,
            'kategori' => 'IZIN',
            'informasi' => 'Tim teknis terjebak kemacetan jalur wisata Setiabudi menuju Lembang.',
            'latitude' => -6.845000,
            'longitude' => 107.605000,
        ]);
        Kronologis::create([
            'id_tiket' => $tiket5->id,
            'timestamp' => (clone $openTime5)->addMinutes(380),
            'user_id' => $teknis1->id,
            'kategori' => 'LINK_UP',
            'informasi' => 'Penyambungan kabel di area tebing Lembang selesai. Link UP normal.',
            'latitude' => -6.820000,
            'longitude' => 107.618000,
        ]);

        ResumePekerjaan::updateOrCreate(
            ['id_tiket' => $tiket5->id],
            [
                'tipe_penanganan' => 'JOINTING_LURUS',
                'joint_closure_type' => 'DOME 48 Core',
                'core_count_jointed' => 24,
                'team_om' => json_encode(['Rian Suryana', 'Budi Santoso']),
                'problem_temuan' => 'Kabel FO tertimpa pohon tumbang di tikungan lereng Lembang.',
                'action' => 'Pemasangan closure baru dan perapihan span kabel 200m menjauhi dahan pohon rawan rapuh.',
                'catatan_tambahan' => 'Perlu koordinasi perapihan ranting pohon berkala dengan dinas pertamanan setempat.',
            ]
        );

        TitikPerbaikan::create([
            'id_tiket' => $tiket5->id,
            'nama_titik' => 'Lereng Hutan Pinus Lembang',
            'latitude' => -6.820000,
            'longitude' => 107.618000,
            'keterangan' => 'Titik pohon tumbang dan pemasangan closure baru',
        ]);

        // =========================================================================
        // NOTIFIKASI LOGS CONTOH
        // =========================================================================
        NotifikasiLog::create([
            'id_tiket' => $tiket1->id,
            'tipe' => 'WA',
            'penerima' => '+6281122334455 (Grup NOC & Management)',
            'pesan' => "🚨 [TIKET CLOSED] BDG-20260920-001\nSegmen: SW BBLU - SW Reog\nStatus: Link UP Normal (MTTR: 5j 15m, SLA: TEPAT)",
            'status' => 'SENT',
            'created_at' => $closeTime1,
        ]);
        NotifikasiLog::create([
            'id_tiket' => $tiket2->id,
            'tipe' => 'INAPP',
            'penerima' => 'HelpDesk & Teknis Shift',
            'pesan' => "⚠️ [STOP CLOCK] Tiket BDG-20260924-002 dijeda sementara (Alasan: AKSES_LOKASI).",
            'status' => 'SENT',
            'created_at' => Carbon::now()->subMinutes(40),
        ]);
    }
}
