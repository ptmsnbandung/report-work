-- ════════════════════════════════════════════════════════════════════════════════════════
-- SQL DUMMY DATA SEEDER UNTUK CONTOH SEMUA PENGGUNAAN DI SHOW BLADE (DETAIL TIKET)
-- Skenario 1: Tiket Closed dengan Jointing Lurus, Splicing JC 96C/48C, Stop Clock, Handover
-- Skenario 2: Tiket In-Progress dengan Manuver Core (Swapping Core OTB & Tiang)
-- ════════════════════════════════════════════════════════════════════════════════════════

-- Pastikan id user referensi ada (1: Admin, 2: Helpdesk, 3: Teknis 1, 4: Teknis 2)
-- Anda dapat menyesuaikan id_user sesuai database lokal Anda.

-- =========================================================================================
-- 1. TIKET #1: BDG-20260914-001 (STATUS: CLOSE, METODE: JOINTING LURUS & JOINT CLOSURE)
-- =========================================================================================

INSERT INTO `tiket` (
    `id`, `no_tiket`, `status_link_impact`, `backbone_segment`, `deskripsi`, 
    `closing_notes_teknisi`, `tipe_penanganan`, `tanggal_open`, `first_response_at`, 
    `response_time_minutes`, `tanggal_close`, `resolved_at`, `status`, 
    `sla_target_minutes`, `mttr_minutes`, `total_stop_clock_minutes`, `is_stop_clock`, 
    `sla_status`, `created_by`, `resolved_by`, `closed_by`, `created_at`, `updated_at`
) VALUES (
    1, 
    'BDG-20260914-001', 
    'Backbone : SW BBLU - SW Reog Down', 
    'SW BBLU - SW Reog', 
    'LOS pada segmen backbone BBLU arah Reog. Indikasi kabel putus tertabrak truk kontainer di jalur crossingan jalan raya.', 
    'Penyambungan jointing 2 titik closure baru (JC1 & JC2) selesai. Semua core 1-24 link backbone UP normal dengan redaman rata-rata -18.2 dB.', 
    'JOINTING_LURUS', 
    '2026-09-14 21:00:00', 
    '2026-09-14 21:15:00', 
    15, 
    '2026-09-15 02:45:00', 
    '2026-09-15 02:35:00', 
    'CLOSE', 
    360, 
    345, 
    30, 
    0, 
    'TEPAT', 
    2, 
    3, 
    2, 
    '2026-09-14 21:00:00', 
    '2026-09-15 02:45:00'
) ON DUPLICATE KEY UPDATE 
    `status_link_impact`=VALUES(`status_link_impact`), 
    `tipe_penanganan`=VALUES(`tipe_penanganan`), 
    `status`=VALUES(`status`);

-- Kronologis Chat Timeline (WhatsApp Style) untuk Tiket #1
INSERT INTO `kronologis` (`id_tiket`, `timestamp`, `user_id`, `kategori`, `informasi`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(1, '2026-09-14 21:15:00', 3, 'IZIN', 'Tim teknis izin bergerak menuju lokasi segment SW BBLU - SW Reog.', -6.91474400, 107.60981000, NOW(), NOW()),
(1, '2026-09-14 21:45:00', 3, 'OTDR', 'Hasil ukur OTDR dari OTB SW BBLU mendeteksi event putus total di jarak 4.8 KM arah Reog.', -6.91890000, 107.61520000, NOW(), NOW()),
(1, '2026-09-14 22:30:00', 4, 'TRACING', 'Titik putus ditemukan di perempatan jalan raya, kabel ditarik truk tronton kontainer yang melintas.', -6.37919900, 106.84655200, NOW(), NOW()),
(1, '2026-09-14 23:10:00', 3, 'MATERIAL', 'Material kabel jumper 24 core 150 meter dan 2 unit Joint Closure (JC) baru tiba di lokasi perbaikan.', -6.37919900, 106.84655200, NOW(), NOW()),
(1, '2026-09-15 00:30:00', 3, 'JOINTING', 'Proses splicing jointing closure 1 (JC-BBLU-01) selesai 24 core, lanjut penarikan kabel span ke JC-BBLU-02.', -6.37919900, 106.84655200, NOW(), NOW()),
(1, '2026-09-15 01:45:00', 4, 'JOINTING', 'Splicing closure 2 (JC-BBLU-02) selesai. Semua core 1 s/d 24 tersambung dengan loss rata-rata 0.03 dB.', -6.37957800, 106.84647500, NOW(), NOW()),
(1, '2026-09-15 02:15:00', 3, 'LINK_UP', 'Link backbone BBLU - Reog terverifikasi UP normal. Trafik stabil, redaman rata-rata -18.2 dB.', -6.37957800, 106.84647500, NOW(), NOW()),
(1, '2026-09-15 02:35:00', 4, 'SELESAI', 'Pemasangan closure ke tiang selesai, perapihan kabel span, dan pembersihan lokasi kerja.', -6.37957800, 106.84647500, NOW(), NOW());

-- Resume Pekerjaan Lapangan untuk Tiket #1
INSERT INTO `resume_pekerjaan` (
    `id_tiket`, `tipe_penanganan`, `joint_closure_type`, `core_count_jointed`, 
    `team_om`, `problem_temuan`, `action`, `catatan_tambahan`, `created_at`, `updated_at`
) VALUES (
    1, 
    'JOINTING_LURUS', 
    'DOME 24-48 Core', 
    24, 
    '["Rian Suryana (Lead)", "Budi Santoso (Splicer)", "Dedi Supriadi (Lineman)"]', 
    'Kabel FO Aerial 24 Core tersangkut truk box kontainer tinggi di perempatan jalan raya crossingan utama.', 
    'Penarikan jumper kabel FO 24 core sepanjang 150 meter, pemasangan 2 titik Joint Closure baru (JC1 & JC2), serta rekoneksi 24 core.', 
    'Rekomendasi penambahan tiang peninggi kabel span crossing agar memenuhi batas standar keselamatan ketinggian jalan raya.', 
    NOW(), 
    NOW()
) ON DUPLICATE KEY UPDATE `tipe_penanganan`=VALUES(`tipe_penanganan`);

-- Material Terpakai untuk Tiket #1
INSERT INTO `material` (`id_tiket`, `nama_material`, `jumlah`, `satuan`, `created_at`, `updated_at`) VALUES
(1, 'Joint Closure (JC) Dome 24-48 Core', 2, 'unit', NOW(), NOW()),
(1, 'Kabel Fiber Optic Aerial 24 Core G.652D', 150, 'meter', NOW(), NOW()),
(1, 'Protection Sleeve 60mm', 48, 'pcs', NOW(), NOW()),
(1, 'Suspension Clamp & Bracket Tiang', 4, 'set', NOW(), NOW()),
(1, 'Stainless Steel Strip Band & Buckle', 6, 'meter', NOW(), NOW());

-- Titik Koordinat Perbaikan untuk Tiket #1
INSERT INTO `titik_perbaikan` (`id_tiket`, `nama_titik`, `latitude`, `longitude`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'JC1 (Joint Closure Span Barat)', -6.37919900, 106.84655200, 'Tiang No 18 depan ruko - Posisi Closure Baru Arah BBLU', NOW(), NOW()),
(1, 'JC2 (Joint Closure Span Timur)', -6.37957800, 106.84647500, 'Tiang No 22 seberang SPBU - Posisi Closure Baru Arah Reog', NOW(), NOW());

-- Data Joint Closure #1 (JC-BBLU-01) - ASET BARU (Kapasitas Eksisting 96 Core disambung Jumper 48 Core)
INSERT INTO `tiket_joint_closures` (
    `id`, `id_tiket`, `nama_closure`, `tipe_closure`, `status_aset`, 
    `kapasitas_kabel_asal`, `jumlah_tube_asal`, `kapasitas_kabel_jumper`, `jumlah_tube_jumper`, 
    `jenis_sambungan`, `lokasi_penempatan`, `latitude`, `longitude`, `catatan`, `created_by`, `created_at`, `updated_at`
) VALUES (
    1, 
    1, 
    'JC-BBLU-01 (Crossing Barat)', 
    'DOME', 
    'ASET_BARU', 
    96, 
    8, 
    48, 
    4, 
    'LURUS_STRAIGHT', 
    'Tiang FO #18 Simpang Lima', 
    -6.37919900, 
    106.84655200, 
    'Pemasangan closure baru akibat pemotongan span putus. Tray 1 & Tray 2 digunakan untuk backbone utama.', 
    3, 
    NOW(), 
    NOW()
) ON DUPLICATE KEY UPDATE `nama_closure`=VALUES(`nama_closure`);

-- Detail Sambungan Tube & Core (Splicing Tray) pada JC-BBLU-01
INSERT INTO `tiket_joint_closure_cores` (`id_joint_closure`, `tube_asal`, `core_asal`, `tube_tujuan`, `core_tujuan`, `status_core`, `loss_db`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Tube 1 (Biru)', 'Core 1 (Biru)', 'Tube 1 (Biru)', 'Core 1 (Biru)', 'TERHUBUNG', 0.02, 'Backbone Primary Link A', NOW(), NOW()),
(1, 'Tube 1 (Biru)', 'Core 2 (Oranye)', 'Tube 1 (Biru)', 'Core 2 (Oranye)', 'TERHUBUNG', 0.03, 'Backbone Primary Link B', NOW(), NOW()),
(1, 'Tube 1 (Biru)', 'Core 3 (Hijau)', 'Tube 1 (Biru)', 'Core 3 (Hijau)', 'TERHUBUNG', 0.01, 'Metro-E Enterprise A', NOW(), NOW()),
(1, 'Tube 1 (Biru)', 'Core 4 (Coklat)', 'Tube 1 (Biru)', 'Core 4 (Coklat)', 'TERHUBUNG', 0.04, 'Metro-E Enterprise B', NOW(), NOW()),
(1, 'Tube 1 (Biru)', 'Core 5 (Abu)', 'Tube 1 (Biru)', 'Core 5 (Abu)', 'TERHUBUNG', 0.02, 'OLT Distribusi Timur', NOW(), NOW()),
(1, 'Tube 1 (Biru)', 'Core 6 (Putih)', 'Tube 1 (Biru)', 'Core 6 (Putih)', 'TERHUBUNG', 0.02, 'OLT Distribusi Barat', NOW(), NOW()),
(1, 'Tube 2 (Oranye)', 'Core 1 (Biru)', NULL, NULL, 'SPARE', NULL, 'Cadangan Tray 2 - Alokasi Spare', NOW(), NOW()),
(1, 'Tube 2 (Oranye)', 'Core 2 (Oranye)', NULL, NULL, 'SPARE', NULL, 'Cadangan Tray 2 - Alokasi Spare', NOW(), NOW()),
(1, 'Tube 2 (Oranye)', 'Core 3 (Hijau)', 'Tube 2 (Oranye)', 'Core 7 (Merah)', 'MANUVER', 0.05, 'Swapping alokasi ke port proteksi', NOW(), NOW()),
(1, 'Tube 3 (Hijau)', 'Core 12 (Toska)', NULL, NULL, 'LOSS_PUTUS', 12.50, 'Core mengalami microbending bawaan', NOW(), NOW());

-- Data Joint Closure #2 (JC-BBLU-02) - EKSISTING (Kapasitas 48 Core disambung Jumper 24 Core)
INSERT INTO `tiket_joint_closures` (
    `id`, `id_tiket`, `nama_closure`, `tipe_closure`, `status_aset`, 
    `kapasitas_kabel_asal`, `jumlah_tube_asal`, `kapasitas_kabel_jumper`, `jumlah_tube_jumper`, 
    `jenis_sambungan`, `lokasi_penempatan`, `latitude`, `longitude`, `catatan`, `created_by`, `created_at`, `updated_at`
) VALUES (
    2, 
    1, 
    'JC-BBLU-02 (Crossing Timur)', 
    'INLINE', 
    'EKSISTING', 
    48, 
    4, 
    24, 
    2, 
    'LURUS_STRAIGHT', 
    'Tiang FO #22 Depan SPBU', 
    -6.37957800, 
    106.84647500, 
    'Pembersihan enclosure lama dan resplicing tray 1 sambungan kabel jumper baru.', 
    4, 
    NOW(), 
    NOW()
) ON DUPLICATE KEY UPDATE `nama_closure`=VALUES(`nama_closure`);

-- Detail Sambungan Tube & Core pada JC-BBLU-02
INSERT INTO `tiket_joint_closure_cores` (`id_joint_closure`, `tube_asal`, `core_asal`, `tube_tujuan`, `core_tujuan`, `status_core`, `loss_db`, `keterangan`, `created_at`, `updated_at`) VALUES
(2, 'Tube 1 (Biru)', 'Core 1 (Biru)', 'Tube 1 (Biru)', 'Core 1 (Biru)', 'TERHUBUNG', 0.02, 'Splicing ke kabel arah SW Reog', NOW(), NOW()),
(2, 'Tube 1 (Biru)', 'Core 2 (Oranye)', 'Tube 1 (Biru)', 'Core 2 (Oranye)', 'TERHUBUNG', 0.03, 'Splicing ke kabel arah SW Reog', NOW(), NOW()),
(2, 'Tube 1 (Biru)', 'Core 3 (Hijau)', 'Tube 1 (Biru)', 'Core 3 (Hijau)', 'TERHUBUNG', 0.02, 'Splicing ke kabel arah SW Reog', NOW(), NOW()),
(2, 'Tube 1 (Biru)', 'Core 4 (Coklat)', 'Tube 1 (Biru)', 'Core 4 (Coklat)', 'TERHUBUNG', 0.02, 'Splicing ke kabel arah SW Reog', NOW(), NOW());

-- Dokumentasi Foto Lapangan untuk Tiket #1
INSERT INTO `dokumentasi` (`id_tiket`, `kategori`, `file_path`, `timestamp`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(1, 'KONDISI_AWAL', 'dokumentasi/dummy_putus.jpg', '2026-09-14 22:35:00', -6.37919900, 106.84655200, NOW(), NOW()),
(1, 'OTDR_TRACE', 'dokumentasi/dummy_otdr.jpg', '2026-09-14 21:50:00', -6.91890000, 107.61520000, NOW(), NOW()),
(1, 'PROSES_REPAIR', 'dokumentasi/dummy_splicing.jpg', '2026-09-15 00:45:00', -6.37919900, 106.84655200, NOW(), NOW()),
(1, 'CLOSURE_INSTALLED', 'dokumentasi/dummy_closure.jpg', '2026-09-15 02:20:00', -6.37957800, 106.84647500, NOW(), NOW()),
(1, 'HASIL_AKHIR', 'dokumentasi/dummy_finish.jpg', '2026-09-15 02:40:00', -6.37957800, 106.84647500, NOW(), NOW());

-- Stop Clock SLA Record untuk Tiket #1 (Jeda 30 Menit Karena Hujan Petir)
INSERT INTO `tiket_stop_clocks` (
    `id_tiket`, `start_time`, `end_time`, `duration_minutes`, `alasan_kategori`, 
    `alasan_detail`, `requested_by`, `stopped_by`, `is_active`, `created_at`, `updated_at`
) VALUES (
    1, 
    '2026-09-14 23:30:00', 
    '2026-09-15 00:00:00', 
    30, 
    'CUACA_BURUK', 
    'Hujan lebat disertai angin kencang dan petir di lokasi penarikan kabel span tiang.', 
    3, 
    2, 
    0, 
    NOW(), 
    NOW()
);

-- Serah Terima Shift (Handover) untuk Tiket #1
INSERT INTO `tiket_handover_shifts` (
    `id_tiket`, `shift_from`, `shift_to`, `user_from_id`, `user_to_id`, `catatan_handover`, `created_at`, `updated_at`
) VALUES (
    1, 
    'Shift Malam (20:00 - 04:00)', 
    'Shift Pagi (04:00 - 12:00)', 
    3, 
    4, 
    'Pekerjaan splicing dan link UP telah selesai 100%. Tiket siap diajukan closing awal dan verifikasi helpdesk.', 
    NOW(), 
    NOW()
);


-- =========================================================================================
-- 2. TIKET #2: BDG-20260918-003 (STATUS: PROSES, METODE: MANUVER CORE / SWAPPING)
-- =========================================================================================

INSERT INTO `tiket` (
    `id`, `no_tiket`, `status_link_impact`, `backbone_segment`, `deskripsi`, 
    `tipe_penanganan`, `tanggal_open`, `first_response_at`, `response_time_minutes`, 
    `tanggal_close`, `resolved_at`, `status`, `sla_target_minutes`, `mttr_minutes`, 
    `total_stop_clock_minutes`, `is_stop_clock`, `sla_status`, `created_by`, `created_at`, `updated_at`
) VALUES (
    2, 
    'BDG-20260918-003', 
    'Backbone : SW BBLU - SW Reog Secondary Path Bending', 
    'SW BBLU - SW Reog', 
    'Jalur proteksi redaman melonjak ke -28dB setelah badai. Dilakukan pengalihan / swapping core cadangan.', 
    'MANUVER_CORE', 
    '2026-09-18 09:30:00', 
    '2026-09-18 09:45:00', 
    15, 
    NULL, 
    NULL, 
    'PROSES', 
    360, 
    NULL, 
    0, 
    0, 
    'NA', 
    2, 
    '2026-09-18 09:30:00', 
    NOW()
) ON DUPLICATE KEY UPDATE 
    `status_link_impact`=VALUES(`status_link_impact`), 
    `tipe_penanganan`=VALUES(`tipe_penanganan`);

-- Kronologis Chat Timeline untuk Tiket #2
INSERT INTO `kronologis` (`id_tiket`, `timestamp`, `user_id`, `kategori`, `informasi`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(2, '2026-09-18 09:45:00', 3, 'IZIN', 'Tim teknis tiba di OTB SW BBLU untuk mapping swapping core proteksi.', -6.91474400, 107.60981000, NOW(), NOW()),
(2, '2026-09-18 10:30:00', 3, 'OTDR', 'Core 1 & 2 pada Tube 2 mengalami bending di KM 1.8. Diputuskan manuver ke Tube 2 Core 7 & 8 yang spare normal.', -6.91890000, 107.61520000, NOW(), NOW()),
(2, '2026-09-18 11:15:00', 4, 'JOINTING', 'Patching jumper patchcord di OTB BBLU dan OTB Reog selesai diselaraskan.', -6.92000000, 107.62000000, NOW(), NOW());

-- Resume Pekerjaan untuk Tiket #2
INSERT INTO `resume_pekerjaan` (
    `id_tiket`, `tipe_penanganan`, `joint_closure_type`, `core_count_jointed`, 
    `team_om`, `problem_temuan`, `action`, `catatan_tambahan`, `created_at`, `updated_at`
) VALUES (
    2, 
    'MANUVER_CORE', 
    'OTB Rack 24 Port', 
    4, 
    '["Rian Suryana (Lead)", "Agus Hermawan (NOC Tech)"]', 
    'Microbending pada Tube 2 Core 1 & Core 2 segmen BBLU-Reog.', 
    'Manuver alokasi traffic utama ke Tube 2 Core 7 & Core 8 (Spare) pada ODF BBLU dan ODF Reog.', 
    'Jalur utama kembali prima dengan redaman -17.8 dB.', 
    NOW(), 
    NOW()
) ON DUPLICATE KEY UPDATE `tipe_penanganan`=VALUES(`tipe_penanganan`);

-- Record Manuver Core (Pemetaan SEBELUM & SESUDAH Perbaikan)
INSERT INTO `manuver_core` (
    `id_tiket`, `lokasi_tipe`, `titik`, `core_asal`, `core_tujuan`, 
    `core_dialihkan`, `titik_kembali`, `tipe`, `status_manuver`, `status_core_aset`, 
    `keterangan`, `created_at`, `updated_at`
) VALUES
-- Titik 1: OTB SW BBLU (SEBELUM)
(2, 'OTB', 'OTB SW BBLU Rack-01', 'Port 01 (Tube 2 Core 1)', 'Port 01 (Tube 2 Core 1 - Bending -28dB)', 'Port 07 (Tube 2 Core 7)', 'OTB SW Reog Port 07', 'SEBELUM', 'TEMPORARY', 'BROKEN_LOSS', 'Alokasi eksisting sebelum dialihkan karena high loss', NOW(), NOW()),
-- Titik 1: OTB SW BBLU (SESUDAH)
(2, 'OTB', 'OTB SW BBLU Rack-01', 'Port 01 (Traffic Utama)', 'Port 07 (Tube 2 Core 7 - Normal -17.8dB)', 'Port 07 (Tube 2 Core 7)', 'OTB SW Reog Port 07', 'SESUDAH', 'TEMPORARY', 'OCCUPIED_MANUVER', 'Hasil manuver ke spare core 7 - Traffic UP Normal', NOW(), NOW()),
-- Titik 2: JC-04 Reog (SEBELUM)
(2, 'CLOSURE_LAPANGAN', 'JC-04 Reog (Tiang #12)', 'Tube 2 Core 2', 'Tube 2 Core 2', 'Tube 2 Core 8', 'ODF Reog', 'SEBELUM', 'TEMPORARY', 'BROKEN_LOSS', 'Core 2 proteksi putus di dalam tray lama', NOW(), NOW()),
-- Titik 2: JC-04 Reog (SESUDAH)
(2, 'CLOSURE_LAPANGAN', 'JC-04 Reog (Tiang #12)', 'Tube 2 Core 2 (Proteksi)', 'Tube 2 Core 8 (Spare Aktif)', 'Tube 2 Core 8', 'ODF Reog', 'SESUDAH', 'TEMPORARY', 'OCCUPIED_MANUVER', 'Swapping berhasil, redaman normal -18.0 dB', NOW(), NOW());

-- Material Tiket #2
INSERT INTO `material` (`id_tiket`, `nama_material`, `jumlah`, `satuan`, `created_at`, `updated_at`) VALUES
(2, 'Patchcord Fiber Optic LC-SC Duplex 3M', 4, 'pcs', NOW(), NOW()),
(2, 'Protection Sleeve 45mm', 8, 'pcs', NOW(), NOW());

-- Titik Perbaikan Tiket #2
INSERT INTO `titik_perbaikan` (`id_tiket`, `nama_titik`, `latitude`, `longitude`, `keterangan`, `created_at`, `updated_at`) VALUES
(2, 'POP SW BBLU (ODF Main)', -6.91474400, 107.60981000, 'Ruang Server Lantai 2', NOW(), NOW()),
(2, 'JC-04 Reog KM 1.8', -6.91890000, 107.61520000, 'Tiang No 12 Depan Bank', NOW(), NOW());

-- Dokumentasi Foto untuk Tiket #2
INSERT INTO `dokumentasi` (`id_tiket`, `kategori`, `file_path`, `timestamp`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(2, 'OTDR_TRACE', 'dokumentasi/dummy_otdr_manuver.jpg', '2026-09-18 10:35:00', -6.91890000, 107.61520000, NOW(), NOW()),
(2, 'PROSES_REPAIR', 'dokumentasi/dummy_otb_patching.jpg', '2026-09-18 11:20:00', -6.91474400, 107.60981000, NOW(), NOW());
