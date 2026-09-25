-- ════════════════════════════════════════════════════════════════════════════════════════
-- SQL SEEDER DATA LENGKAP: SELURUH FITUR SISTEM TIKETING FIBER OPTIC (PT MSN)
-- Kompatibel langsung dengan phpMyAdmin, HeidiSQL, DBeaver, dan MySQL Client
-- ════════════════════════════════════════════════════════════════════════════════════════

SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- 0. BERSIHKAN DATA TRANSAKSI SEBELUMNYA (OPSIONAL / FRESH SEED)
-- -----------------------------------------------------------------------------
DELETE FROM `tiket_joint_closure_cores`;
DELETE FROM `tiket_joint_closures`;
DELETE FROM `manuver_core`;
DELETE FROM `tiket_stop_clocks`;
DELETE FROM `tiket_handover_shifts`;
DELETE FROM `dokumentasi`;
DELETE FROM `titik_perbaikan`;
DELETE FROM `material`;
DELETE FROM `resume_pekerjaan`;
DELETE FROM `kronologis`;
DELETE FROM `notifikasi_log`;
DELETE FROM `tiket`;

ALTER TABLE `tiket` AUTO_INCREMENT = 1;
ALTER TABLE `kronologis` AUTO_INCREMENT = 1;
ALTER TABLE `resume_pekerjaan` AUTO_INCREMENT = 1;
ALTER TABLE `material` AUTO_INCREMENT = 1;
ALTER TABLE `titik_perbaikan` AUTO_INCREMENT = 1;
ALTER TABLE `dokumentasi` AUTO_INCREMENT = 1;
ALTER TABLE `tiket_stop_clocks` AUTO_INCREMENT = 1;
ALTER TABLE `tiket_handover_shifts` AUTO_INCREMENT = 1;
ALTER TABLE `tiket_joint_closures` AUTO_INCREMENT = 1;
ALTER TABLE `tiket_joint_closure_cores` AUTO_INCREMENT = 1;
ALTER TABLE `manuver_core` AUTO_INCREMENT = 1;
ALTER TABLE `notifikasi_log` AUTO_INCREMENT = 1;

-- =============================================================================
-- 1. MASTER SLA
-- =============================================================================
INSERT INTO `master_sla` (`backbone_segment`, `sla_target_minutes`, `created_at`, `updated_at`) VALUES
('SW BBLU - SW Reog', 360, NOW(), NOW()),
('SW Cikutra - SW Dago', 360, NOW(), NOW()),
('SW Soekarno Hatta - SW Buah Batu', 360, NOW(), NOW()),
('SW Pasteur - SW Sukajadi', 240, NOW(), NOW()),
('SW Kopo - SW Cibaduyut', 180, NOW(), NOW()),
('SW Ujungberung - SW Cibiru', 180, NOW(), NOW()),
('SW Cimahi - SW Padalarang', 240, NOW(), NOW()),
('SW Setiabudi - SW Lembang', 300, NOW(), NOW())
ON DUPLICATE KEY UPDATE `sla_target_minutes` = VALUES(`sla_target_minutes`);

-- =============================================================================
-- 2. MASTER MATERIAL
-- =============================================================================
INSERT INTO `master_materials` (`kode_material`, `nama_material`, `satuan`, `stok_tersedia`, `keterangan`, `is_active`, `created_at`, `updated_at`) VALUES
('MAT-FO-01', 'Kabel FO ADSS 24 Core', 'Meter', 2500, 'Kabel FO Aerial ADSS 24 core G.652D', 1, NOW(), NOW()),
('MAT-FO-02', 'Kabel FO ADSS 48 Core', 'Meter', 1800, 'Kabel FO Aerial ADSS 48 core G.652D', 1, NOW(), NOW()),
('MAT-CLS-01', 'Closure Dome 24 Core', 'Unit', 35, 'Joint Closure Dome 24 core outdoor IP68', 1, NOW(), NOW()),
('MAT-CLS-02', 'Closure Inline 48 Core', 'Unit', 20, 'Joint Closure Inline 48 core outdoor IP68', 1, NOW(), NOW()),
('MAT-PROT-01', 'Protection Sleeve 60mm', 'Pcs', 500, 'Sleeve pelindung sambungan core fusion splicer', 1, NOW(), NOW()),
('MAT-ACC-01', 'Suspension Clamp FO', 'Set', 120, 'Aksesoris gantungan kabel di tiang', 1, NOW(), NOW()),
('MAT-ACC-02', 'Dead End Clamp / Tension Clamp', 'Set', 95, 'Klem penarik kabel tiang ujung/sudut', 1, NOW(), NOW()),
('MAT-ACC-03', 'Spiral Protection / Wrap Tube', 'Meter', 300, 'Pelindung kabel dari gesekan ranting pohon', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE `stok_tersedia` = VALUES(`stok_tersedia`);

-- =============================================================================
-- 3. TIKET #1: BDG-20260920-001 (STATUS: CLOSE, SLA: TEPAT, JOINTING & CLOSURE)
-- =============================================================================
INSERT INTO `tiket` (
    `id`, `no_tiket`, `status_link_impact`, `backbone_segment`, `deskripsi`, 
    `closing_notes_teknisi`, `tipe_penanganan`, `tanggal_open`, `first_response_at`, 
    `response_time_minutes`, `tanggal_close`, `resolved_at`, `status`, 
    `sla_target_minutes`, `mttr_minutes`, `total_stop_clock_minutes`, `is_stop_clock`, 
    `sla_status`, `created_by`, `resolved_by`, `closed_by`, `created_at`, `updated_at`
) VALUES (
    1, 
    'BDG-20260920-001', 
    'Backbone : SW BBLU - SW Reog Down', 
    'SW BBLU - SW Reog', 
    'LOS total pada link backbone BBLU arah Reog. Indikasi kabel FO 24 Core putus akibat tersangkut truk kontainer di crossingan jalan raya.', 
    'Penyambungan jointing 2 titik closure baru (JC1 & JC2) selesai. Semua 24 core link backbone UP normal dengan redaman optik rata-rata -18.2 dB.', 
    'JOINTING_LURUS', 
    DATE_SUB(NOW(), INTERVAL 48 HOUR), 
    DATE_ADD(DATE_SUB(NOW(), INTERVAL 48 HOUR), INTERVAL 15 MINUTE), 
    15, 
    DATE_ADD(DATE_SUB(NOW(), INTERVAL 48 HOUR), INTERVAL 345 MINUTE), 
    DATE_ADD(DATE_SUB(NOW(), INTERVAL 48 HOUR), INTERVAL 315 MINUTE), 
    'CLOSE', 
    360, 
    315, 
    30, 
    0, 
    'TEPAT', 
    2, 
    3, 
    2, 
    DATE_SUB(NOW(), INTERVAL 48 HOUR), 
    NOW()
);

-- Kronologis Tiket #1
INSERT INTO `kronologis` (`id_tiket`, `timestamp`, `user_id`, `kategori`, `informasi`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(1, DATE_ADD(DATE_SUB(NOW(), INTERVAL 48 HOUR), INTERVAL 15 MINUTE), 3, 'IZIN', 'Tim teknis izin bergerak menuju lokasi insiden segment SW BBLU - SW Reog.', -6.91474400, 107.60981000, NOW(), NOW()),
(1, DATE_ADD(DATE_SUB(NOW(), INTERVAL 48 HOUR), INTERVAL 45 MINUTE), 3, 'OTDR', 'Hasil ukur OTDR dari OTB SW BBLU mendeteksi event putus total pada jarak 4.8 KM arah Reog.', -6.91890000, 107.61520000, NOW(), NOW()),
(1, DATE_ADD(DATE_SUB(NOW(), INTERVAL 48 HOUR), INTERVAL 90 MINUTE), 4, 'TRACING', 'Titik fisik kabel putus ditemukan di perempatan jalan raya, kabel ditarik truk tronton kontainer.', -6.37919900, 106.84655200, NOW(), NOW()),
(1, DATE_ADD(DATE_SUB(NOW(), INTERVAL 48 HOUR), INTERVAL 130 MINUTE), 3, 'MATERIAL', 'Material kabel jumper 24 core 150m dan 2 unit Joint Closure Dome tiba di lokasi perbaikan.', -6.37919900, 106.84655200, NOW(), NOW()),
(1, DATE_ADD(DATE_SUB(NOW(), INTERVAL 48 HOUR), INTERVAL 210 MINUTE), 3, 'JOINTING', 'Splicing closure 1 (JC-BBLU-01) selesai 24 core, lanjut penarikan kabel span ke JC-BBLU-02.', -6.37919900, 106.84655200, NOW(), NOW()),
(1, DATE_ADD(DATE_SUB(NOW(), INTERVAL 48 HOUR), INTERVAL 285 MINUTE), 4, 'JOINTING', 'Splicing closure 2 (JC-BBLU-02) rampung. Seluruh core 1-24 tersambung dengan rata-rata loss 0.03 dB.', -6.37957800, 106.84647500, NOW(), NOW()),
(1, DATE_ADD(DATE_SUB(NOW(), INTERVAL 48 HOUR), INTERVAL 315 MINUTE), 3, 'LINK_UP', 'Link backbone BBLU - Reog terverifikasi UP normal. Trafik stabil, redaman rata-rata -18.2 dB.', -6.37957800, 106.84647500, NOW(), NOW()),
(1, DATE_ADD(DATE_SUB(NOW(), INTERVAL 48 HOUR), INTERVAL 335 MINUTE), 4, 'SELESAI', 'Pemasangan closure ke tiang selesai, perapihan kabel span, dan pembersihan area kerja.', -6.37957800, 106.84647500, NOW(), NOW());

-- Stop Clock Tiket #1
INSERT INTO `tiket_stop_clocks` (`id_tiket`, `start_time`, `end_time`, `duration_minutes`, `alasan_kategori`, `alasan_detail`, `requested_by`, `stopped_by`, `is_active`, `created_at`, `updated_at`) VALUES
(1, DATE_ADD(DATE_SUB(NOW(), INTERVAL 48 HOUR), INTERVAL 140 MINUTE), DATE_ADD(DATE_SUB(NOW(), INTERVAL 48 HOUR), INTERVAL 170 MINUTE), 30, 'CUACA_BURUK', 'Hujan badai disertai petir lebat di lokasi span tiang, proses penarikan kabel ditunda demi K3 teknisi.', 3, 2, 0, NOW(), NOW());

-- Handover Shift Tiket #1
INSERT INTO `tiket_handover_shifts` (`id_tiket`, `shift_from`, `shift_to`, `user_from_id`, `user_to_id`, `catatan_handover`, `created_at`, `updated_at`) VALUES
(1, 'Shift Malam (20:00 - 04:00)', 'Shift Pagi (04:00 - 12:00)', 3, 4, 'Pekerjaan jointing 2 titik closure telah tuntas dan link UP normal. Mohon dipantau kestabilan link selama masa observasi 2 jam.', NOW(), NOW());

-- Resume Pekerjaan Tiket #1
INSERT INTO `resume_pekerjaan` (`id_tiket`, `tipe_penanganan`, `joint_closure_type`, `core_count_jointed`, `team_om`, `problem_temuan`, `action`, `catatan_tambahan`, `created_at`, `updated_at`) VALUES
(1, 'JOINTING_LURUS', 'DOME 24-48 Core', 24, '["Rian Suryana (Lead)", "Budi Santoso (Splicer)", "Dedi Supriadi (Lineman)"]', 'Kabel FO Aerial 24 Core terputus akibat tersangkut truk box kontainer tinggi di perempatan jalan raya crossing utama.', 'Penarikan jumper kabel FO 24 core sepanjang 150 meter, pemasangan 2 titik Joint Closure baru (JC1 & JC2), serta rekoneksi 24 core.', 'Rekomendasi penambahan tiang peninggi kabel span crossing agar memenuhi batas standar keselamatan ketinggian jalan raya (minimal 6 meter).', NOW(), NOW());

-- Material Tiket #1
INSERT INTO `material` (`id_tiket`, `nama_material`, `jumlah`, `satuan`, `created_at`, `updated_at`) VALUES
(1, 'Joint Closure Dome 24 Core', 2, 'Unit', NOW(), NOW()),
(1, 'Kabel FO ADSS 24 Core', 150, 'Meter', NOW(), NOW()),
(1, 'Protection Sleeve 60mm', 48, 'Pcs', NOW(), NOW()),
(1, 'Suspension Clamp FO', 4, 'Set', NOW(), NOW()),
(1, 'Dead End Clamp / Tension Clamp', 4, 'Set', NOW(), NOW());

-- Titik Perbaikan Tiket #1
INSERT INTO `titik_perbaikan` (`id_tiket`, `nama_titik`, `latitude`, `longitude`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'JC1 (Joint Closure Span Barat)', -6.37919900, 106.84655200, 'Tiang No 18 depan ruko - Posisi Closure Baru Arah BBLU', NOW(), NOW()),
(1, 'JC2 (Joint Closure Span Timur)', -6.37957800, 106.84647500, 'Tiang No 22 seberang SPBU - Posisi Closure Baru Arah Reog', NOW(), NOW());

-- Joint Closures Tiket #1
INSERT INTO `tiket_joint_closures` (`id`, `id_tiket`, `nama_closure`, `tipe_closure`, `status_aset`, `kapasitas_kabel_asal`, `jumlah_tube_asal`, `kapasitas_kabel_jumper`, `jumlah_tube_jumper`, `jenis_sambungan`, `lokasi_penempatan`, `latitude`, `longitude`, `catatan`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'JC-BBLU-01 (Crossing Barat)', 'DOME', 'ASET_BARU', 96, 8, 48, 4, 'LURUS_STRAIGHT', 'Tiang FO #18 Simpang Lima', -6.37919900, 106.84655200, 'Pemasangan closure baru akibat pemotongan span putus. Tray 1 & Tray 2 digunakan untuk alokasi backbone utama.', 3, NOW(), NOW()),
(2, 1, 'JC-BBLU-02 (Crossing Timur)', 'INLINE', 'EKSISTING', 48, 4, 24, 2, 'LURUS_STRAIGHT', 'Tiang FO #22 Depan SPBU', -6.37957800, 106.84647500, 'Pembersihan enclosure lama dan resplicing tray 1 sambungan kabel jumper baru.', 4, NOW(), NOW());

-- Cores Splicing JC-BBLU-01
INSERT INTO `tiket_joint_closure_cores` (`id_joint_closure`, `tube_asal`, `core_asal`, `tube_tujuan`, `core_tujuan`, `status_core`, `loss_db`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Tube 1 (Biru)', 'Core 1 (Biru)', 'Tube 1 (Biru)', 'Core 1 (Biru)', 'TERHUBUNG', 0.02, 'Backbone Primary Link A', NOW(), NOW()),
(1, 'Tube 1 (Biru)', 'Core 2 (Oranye)', 'Tube 1 (Biru)', 'Core 2 (Oranye)', 'TERHUBUNG', 0.03, 'Backbone Primary Link B', NOW(), NOW()),
(1, 'Tube 1 (Biru)', 'Core 3 (Hijau)', 'Tube 1 (Biru)', 'Core 3 (Hijau)', 'TERHUBUNG', 0.01, 'Metro-E Enterprise Link 1', NOW(), NOW()),
(1, 'Tube 1 (Biru)', 'Core 4 (Coklat)', 'Tube 1 (Biru)', 'Core 4 (Coklat)', 'TERHUBUNG', 0.04, 'Metro-E Enterprise Link 2', NOW(), NOW()),
(1, 'Tube 1 (Biru)', 'Core 5 (Abu-abu)', 'Tube 1 (Biru)', 'Core 5 (Abu-abu)', 'TERHUBUNG', 0.02, 'OLT Distribusi Timur', NOW(), NOW()),
(1, 'Tube 1 (Biru)', 'Core 6 (Putih)', 'Tube 1 (Biru)', 'Core 6 (Putih)', 'TERHUBUNG', 0.02, 'OLT Distribusi Barat', NOW(), NOW()),
(1, 'Tube 2 (Oranye)', 'Core 1 (Biru)', NULL, NULL, 'SPARE', NULL, 'Cadangan Tray 2 - Alokasi Spare', NOW(), NOW()),
(1, 'Tube 2 (Oranye)', 'Core 2 (Oranye)', NULL, NULL, 'SPARE', NULL, 'Cadangan Tray 2 - Alokasi Spare', NOW(), NOW()),
(1, 'Tube 2 (Oranye)', 'Core 3 (Hijau)', 'Tube 2 (Oranye)', 'Core 7 (Merah)', 'MANUVER', 0.05, 'Swapping alokasi ke port proteksi', NOW(), NOW()),
(1, 'Tube 3 (Hijau)', 'Core 12 (Toska)', NULL, NULL, 'LOSS_PUTUS', 12.50, 'Core mengalami microbending bawaan kabel lama', NOW(), NOW());

-- Cores Splicing JC-BBLU-02
INSERT INTO `tiket_joint_closure_cores` (`id_joint_closure`, `tube_asal`, `core_asal`, `tube_tujuan`, `core_tujuan`, `status_core`, `loss_db`, `keterangan`, `created_at`, `updated_at`) VALUES
(2, 'Tube 1 (Biru)', 'Core 1 (Biru)', 'Tube 1 (Biru)', 'Core 1 (Biru)', 'TERHUBUNG', 0.02, 'Splicing ke kabel arah SW Reog', NOW(), NOW()),
(2, 'Tube 1 (Biru)', 'Core 2 (Oranye)', 'Tube 1 (Biru)', 'Core 2 (Oranye)', 'TERHUBUNG', 0.03, 'Splicing ke kabel arah SW Reog', NOW(), NOW()),
(2, 'Tube 1 (Biru)', 'Core 3 (Hijau)', 'Tube 1 (Biru)', 'Core 3 (Hijau)', 'TERHUBUNG', 0.02, 'Splicing ke kabel arah SW Reog', NOW(), NOW()),
(2, 'Tube 1 (Biru)', 'Core 4 (Coklat)', 'Tube 1 (Biru)', 'Core 4 (Coklat)', 'TERHUBUNG', 0.02, 'Splicing ke kabel arah SW Reog', NOW(), NOW());

-- Dokumentasi Tiket #1
INSERT INTO `dokumentasi` (`id_tiket`, `kategori`, `file_path`, `timestamp`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(1, 'KONDISI_AWAL', 'dokumentasi/dummy_putus.jpg', DATE_ADD(DATE_SUB(NOW(), INTERVAL 48 HOUR), INTERVAL 45 MINUTE), -6.37919900, 106.84655200, NOW(), NOW()),
(1, 'OTDR_TRACE', 'dokumentasi/dummy_otdr.jpg', DATE_ADD(DATE_SUB(NOW(), INTERVAL 48 HOUR), INTERVAL 50 MINUTE), -6.91890000, 107.61520000, NOW(), NOW()),
(1, 'PROSES_REPAIR', 'dokumentasi/dummy_splicing.jpg', DATE_ADD(DATE_SUB(NOW(), INTERVAL 48 HOUR), INTERVAL 210 MINUTE), -6.37919900, 106.84655200, NOW(), NOW()),
(1, 'HASIL_AKHIR', 'dokumentasi/dummy_finish.jpg', DATE_ADD(DATE_SUB(NOW(), INTERVAL 48 HOUR), INTERVAL 335 MINUTE), -6.37957800, 106.84647500, NOW(), NOW());


-- =============================================================================
-- 4. TIKET #2: BDG-20260924-002 (STATUS: PROSES, MANUVER CORE, STOP CLOCK AKTIF)
-- =============================================================================
INSERT INTO `tiket` (
    `id`, `no_tiket`, `status_link_impact`, `backbone_segment`, `deskripsi`, 
    `closing_notes_teknisi`, `tipe_penanganan`, `tanggal_open`, `first_response_at`, 
    `response_time_minutes`, `tanggal_close`, `resolved_at`, `status`, 
    `sla_target_minutes`, `mttr_minutes`, `total_stop_clock_minutes`, `is_stop_clock`, 
    `sla_status`, `created_by`, `resolved_by`, `closed_by`, `created_at`, `updated_at`
) VALUES (
    2, 
    'BDG-20260924-002', 
    'Backbone : SW Cikutra - SW Dago High Loss (-29dB)', 
    'SW Cikutra - SW Dago', 
    'Kenaikan redaman drastis pada link proteksi Cikutra-Dago. Terjadi penurunan performa throughput. Dilakukan pengalihan / swapping port core spare.', 
    NULL, 
    'MANUVER_CORE', 
    DATE_SUB(NOW(), INTERVAL 4 HOUR), 
    DATE_ADD(DATE_SUB(NOW(), INTERVAL 4 HOUR), INTERVAL 12 MINUTE), 
    12, 
    NULL, 
    NULL, 
    'PROSES', 
    360, 
    NULL, 
    45, 
    1, 
    'NA', 
    2, 
    NULL, 
    NULL, 
    DATE_SUB(NOW(), INTERVAL 4 HOUR), 
    NOW()
);

-- Kronologis Tiket #2
INSERT INTO `kronologis` (`id_tiket`, `timestamp`, `user_id`, `kategori`, `informasi`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(2, DATE_ADD(DATE_SUB(NOW(), INTERVAL 4 HOUR), INTERVAL 12 MINUTE), 3, 'IZIN', 'Tim teknis menerima eskalasi dan tiba di OTB POP SW Cikutra untuk investigasi link.', -6.89500000, 107.63200000, NOW(), NOW()),
(2, DATE_ADD(DATE_SUB(NOW(), INTERVAL 4 HOUR), INTERVAL 45 MINUTE), 3, 'OTDR', 'Core 1 & 2 pada Tube 2 mengalami microbending di KM 2.3. Diputuskan manuver alokasi traffic ke Tube 2 Core 7 & 8 (Spare Aktif).', -6.89200000, 107.62800000, NOW(), NOW()),
(2, DATE_ADD(DATE_SUB(NOW(), INTERVAL 4 HOUR), INTERVAL 90 MINUTE), 4, 'JOINTING', 'Patching jumper patchcord di OTB Rack Cikutra dan OTB Dago selesai diselaraskan.', -6.88500000, 107.61500000, NOW(), NOW()),
(2, DATE_ADD(DATE_SUB(NOW(), INTERVAL 4 HOUR), INTERVAL 135 MINUTE), 3, 'LAIN', 'Menunggu izin akses gedung pengelola POP Dago untuk verifikasi redaman akhir pada power meter OLT.', -6.88500000, 107.61500000, NOW(), NOW());

-- Stop Clock Aktif Tiket #2
INSERT INTO `tiket_stop_clocks` (`id_tiket`, `start_time`, `end_time`, `duration_minutes`, `alasan_kategori`, `alasan_detail`, `requested_by`, `stopped_by`, `is_active`, `created_at`, `updated_at`) VALUES
(2, DATE_ADD(DATE_SUB(NOW(), INTERVAL 4 HOUR), INTERVAL 135 MINUTE), NULL, 45, 'AKSES_LOKASI', 'Menunggu verifikasi kartu izin masuk gedung server POP Dago oleh pihak security pengelola kawasan.', 3, NULL, 1, NOW(), NOW());

-- Handover Shift Tiket #2
INSERT INTO `tiket_handover_shifts` (`id_tiket`, `shift_from`, `shift_to`, `user_from_id`, `user_to_id`, `catatan_handover`, `created_at`, `updated_at`) VALUES
(2, 'Shift Pagi (08:00 - 16:00)', 'Shift Sore (16:00 - 00:00)', 3, 4, 'Manuver jumper patchcord di OTB Cikutra telah selesai. Tinggal menunggu approval akses POP Dago untuk cross-check power meter port 7.', NOW(), NOW());

-- Resume Pekerjaan Tiket #2
INSERT INTO `resume_pekerjaan` (`id_tiket`, `tipe_penanganan`, `joint_closure_type`, `core_count_jointed`, `team_om`, `problem_temuan`, `action`, `catatan_tambahan`, `created_at`, `updated_at`) VALUES
(2, 'MANUVER_CORE', 'OTB Rack 24 Port & FAT Box', 4, '["Rian Suryana (Lead)", "Agus Hermawan (NOC Tech)"]', 'Microbending pada Tube 2 Core 1 & Core 2 segmen Cikutra-Dago mengakibatkan redaman -29dB.', 'Manuver alokasi traffic utama ke Tube 2 Core 7 & Core 8 (Spare) pada ODF Cikutra dan ODF Dago.', 'Jalur utama kembali prima dengan redaman normal -17.8 dB setelah manuver core.', NOW(), NOW());

-- Manuver Core Mapping Tiket #2
INSERT INTO `manuver_core` (`id_tiket`, `lokasi_tipe`, `titik`, `core_asal`, `core_tujuan`, `core_dialihkan`, `titik_kembali`, `tipe`, `status_manuver`, `status_core_aset`, `keterangan`, `created_at`, `updated_at`) VALUES
(2, 'OTB', 'OTB SW Cikutra Rack-01', 'Port 01 (Tube 2 Core 1)', 'Port 01 (Tube 2 Core 1 - Bending -29dB)', 'Port 07 (Tube 2 Core 7)', 'OTB SW Dago Port 07', 'SEBELUM', 'TEMPORARY', 'BROKEN_LOSS', 'Alokasi eksisting sebelum dialihkan karena high loss', NOW(), NOW()),
(2, 'OTB', 'OTB SW Cikutra Rack-01', 'Port 01 (Traffic Utama)', 'Port 07 (Tube 2 Core 7 - Normal -17.8dB)', 'Port 07 (Tube 2 Core 7)', 'OTB SW Dago Port 07', 'SESUDAH', 'TEMPORARY', 'OCCUPIED_MANUVER', 'Hasil manuver ke spare core 7 - Traffic UP Normal', NOW(), NOW()),
(2, 'CLOSURE_LAPANGAN', 'JC-03 Dago (Tiang #12)', 'Tube 2 Core 2', 'Tube 2 Core 2', 'Tube 2 Core 8', 'ODF Dago', 'SEBELUM', 'TEMPORARY', 'BROKEN_LOSS', 'Core 2 proteksi putus di dalam tray lama', NOW(), NOW()),
(2, 'CLOSURE_LAPANGAN', 'JC-03 Dago (Tiang #12)', 'Tube 2 Core 2 (Proteksi)', 'Tube 2 Core 8 (Spare Aktif)', 'Tube 2 Core 8', 'ODF Dago', 'SESUDAH', 'TEMPORARY', 'OCCUPIED_MANUVER', 'Swapping berhasil, redaman normal -18.0 dB', NOW(), NOW());

-- Material Tiket #2
INSERT INTO `material` (`id_tiket`, `nama_material`, `jumlah`, `satuan`, `created_at`, `updated_at`) VALUES
(2, 'Patchcord Fiber Optic LC-SC Duplex 3M', 4, 'Pcs', NOW(), NOW()),
(2, 'Protection Sleeve 45mm', 8, 'Pcs', NOW(), NOW());

-- Titik Perbaikan Tiket #2
INSERT INTO `titik_perbaikan` (`id_tiket`, `nama_titik`, `latitude`, `longitude`, `keterangan`, `created_at`, `updated_at`) VALUES
(2, 'POP SW Cikutra (ODF Main)', -6.89500000, 107.63200000, 'Ruang Server Lantai 2', NOW(), NOW()),
(2, 'JC-03 Dago KM 2.3', -6.88500000, 107.61500000, 'Tiang No 12 Depan Simpang Dago', NOW(), NOW());


-- =============================================================================
-- 5. TIKET #3: BDG-20260925-003 (STATUS: OPEN - TIKET BARU)
-- =============================================================================
INSERT INTO `tiket` (
    `id`, `no_tiket`, `status_link_impact`, `backbone_segment`, `deskripsi`, 
    `closing_notes_teknisi`, `tipe_penanganan`, `tanggal_open`, `first_response_at`, 
    `response_time_minutes`, `tanggal_close`, `resolved_at`, `status`, 
    `sla_target_minutes`, `mttr_minutes`, `total_stop_clock_minutes`, `is_stop_clock`, 
    `sla_status`, `created_by`, `resolved_by`, `closed_by`, `created_at`, `updated_at`
) VALUES (
    3, 
    'BDG-20260925-003', 
    'Backbone : SW Soekarno Hatta - SW Buah Batu Alarm Major', 
    'SW Soekarno Hatta - SW Buah Batu', 
    'Alarm major optical power receive drop di bawah ambang batas (-32 dBm). Indikasi bending atau tarikan kabel pada span jalur bypass Soekarno Hatta.', 
    NULL, 
    'JOINTING_LURUS', 
    DATE_SUB(NOW(), INTERVAL 35 MINUTE), 
    NULL, 
    NULL, 
    NULL, 
    NULL, 
    'OPEN', 
    360, 
    NULL, 
    0, 
    0, 
    'NA', 
    2, 
    NULL, 
    NULL, 
    DATE_SUB(NOW(), INTERVAL 35 MINUTE), 
    NOW()
);

INSERT INTO `kronologis` (`id_tiket`, `timestamp`, `user_id`, `kategori`, `informasi`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(3, DATE_SUB(NOW(), INTERVAL 35 MINUTE), 2, 'LAIN', 'Tiket gangguan dibuat oleh NOC HelpDesk berdasarkan notifikasi NMS Zabbix (Alarm Major Optical Loss).', -6.94000000, 107.63500000, NOW(), NOW());


-- =============================================================================
-- 6. TIKET #4: BDG-20260925-004 (STATUS: PROSES - PERGANTIAN SPAN KABEL)
-- =============================================================================
INSERT INTO `tiket` (
    `id`, `no_tiket`, `status_link_impact`, `backbone_segment`, `deskripsi`, 
    `closing_notes_teknisi`, `tipe_penanganan`, `tanggal_open`, `first_response_at`, 
    `response_time_minutes`, `tanggal_close`, `resolved_at`, `status`, 
    `sla_target_minutes`, `mttr_minutes`, `total_stop_clock_minutes`, `is_stop_clock`, 
    `sla_status`, `created_by`, `resolved_by`, `closed_by`, `created_at`, `updated_at`
) VALUES (
    4, 
    'BDG-20260925-004', 
    'Backbone : SW Pasteur - SW Sukajadi Down', 
    'SW Pasteur - SW Sukajadi', 
    'Kabel FO 24C putus terbakar akibat percikan api trafo PLN di tiang simpang Pasteur.', 
    NULL, 
    'PERGANTIAN_KABEL', 
    DATE_SUB(NOW(), INTERVAL 135 MINUTE), 
    DATE_ADD(DATE_SUB(NOW(), INTERVAL 135 MINUTE), INTERVAL 10 MINUTE), 
    10, 
    NULL, 
    NULL, 
    'PROSES', 
    240, 
    NULL, 
    0, 
    0, 
    'NA', 
    2, 
    NULL, 
    NULL, 
    DATE_SUB(NOW(), INTERVAL 135 MINUTE), 
    NOW()
);

INSERT INTO `kronologis` (`id_tiket`, `timestamp`, `user_id`, `kategori`, `informasi`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(4, DATE_ADD(DATE_SUB(NOW(), INTERVAL 135 MINUTE), INTERVAL 10 MINUTE), 4, 'IZIN', 'Tim teknis meluncur ke lokasi tiang trafo Simpang Pasteur.', -6.89800000, 107.59500000, NOW(), NOW()),
(4, DATE_ADD(DATE_SUB(NOW(), INTERVAL 135 MINUTE), INTERVAL 35 MINUTE), 4, 'TRACING', 'Kabel FO hangus terbakar sepanjang 60 meter. Diperlukan span kabel pengganti 100m dan 2 closure.', -6.89800000, 107.59500000, NOW(), NOW()),
(4, DATE_ADD(DATE_SUB(NOW(), INTERVAL 135 MINUTE), INTERVAL 70 MINUTE), 3, 'MATERIAL', 'Kabel FO ADSS 24C 100m, Closure Dome, dan klem tiang tiba di lokasi.', -6.89800000, 107.59500000, NOW(), NOW()),
(4, DATE_ADD(DATE_SUB(NOW(), INTERVAL 135 MINUTE), INTERVAL 110 MINUTE), 4, 'JOINTING', 'Proses penarikan span baru selesai, teknisi sedang melakukan splicing di closure sisi barat.', -6.89800000, 107.59500000, NOW(), NOW());

INSERT INTO `titik_perbaikan` (`id_tiket`, `nama_titik`, `latitude`, `longitude`, `keterangan`, `created_at`, `updated_at`) VALUES
(4, 'Tiang Trafo Simpang Pasteur', -6.89800000, 107.59500000, 'Lokasi kabel terbakar dan penarikan span baru', NOW(), NOW());

INSERT INTO `material` (`id_tiket`, `nama_material`, `jumlah`, `satuan`, `created_at`, `updated_at`) VALUES
(4, 'Kabel FO ADSS 24 Core', 100, 'Meter', NOW(), NOW()),
(4, 'Closure Dome 24 Core', 2, 'Unit', NOW(), NOW());


-- =============================================================================
-- 7. TIKET #5: BDG-20260918-005 (STATUS: CLOSE, OVER SLA / LEBIH UNTUK METRIK KPI)
-- =============================================================================
INSERT INTO `tiket` (
    `id`, `no_tiket`, `status_link_impact`, `backbone_segment`, `deskripsi`, 
    `closing_notes_teknisi`, `tipe_penanganan`, `tanggal_open`, `first_response_at`, 
    `response_time_minutes`, `tanggal_close`, `resolved_at`, `status`, 
    `sla_target_minutes`, `mttr_minutes`, `total_stop_clock_minutes`, `is_stop_clock`, 
    `sla_status`, `created_by`, `resolved_by`, `closed_by`, `created_at`, `updated_at`
) VALUES (
    5, 
    'BDG-20260918-005', 
    'Backbone : SW Setiabudi - SW Lembang Cut', 
    'SW Setiabudi - SW Lembang', 
    'Kabel tertimpa pohon tumbang di lereng pegunungan Lembang. Akses lokasi terhambat kemacetan wisata dan jalur tebing curam.', 
    'Penyambungan darurat dengan kabel jumper 200m selesai. Link normal dengan redaman -19.5 dB.', 
    'JOINTING_LURUS', 
    DATE_SUB(NOW(), INTERVAL 120 HOUR), 
    DATE_ADD(DATE_SUB(NOW(), INTERVAL 120 HOUR), INTERVAL 20 MINUTE), 
    20, 
    DATE_ADD(DATE_SUB(NOW(), INTERVAL 120 HOUR), INTERVAL 405 MINUTE), 
    DATE_ADD(DATE_SUB(NOW(), INTERVAL 120 HOUR), INTERVAL 380 MINUTE), 
    'CLOSE', 
    300, 
    380, 
    0, 
    0, 
    'LEBIH', 
    2, 
    3, 
    2, 
    DATE_SUB(NOW(), INTERVAL 120 HOUR), 
    NOW()
);

INSERT INTO `kronologis` (`id_tiket`, `timestamp`, `user_id`, `kategori`, `informasi`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(5, DATE_ADD(DATE_SUB(NOW(), INTERVAL 120 HOUR), INTERVAL 20 MINUTE), 3, 'IZIN', 'Tim teknis terjebak kemacetan jalur wisata Setiabudi menuju Lembang.', -6.84500000, 107.60500000, NOW(), NOW()),
(5, DATE_ADD(DATE_SUB(NOW(), INTERVAL 120 HOUR), INTERVAL 380 MINUTE), 3, 'LINK_UP', 'Penyambungan kabel di area tebing Lembang selesai. Link UP normal.', -6.82000000, 107.61800000, NOW(), NOW());

INSERT INTO `resume_pekerjaan` (`id_tiket`, `tipe_penanganan`, `joint_closure_type`, `core_count_jointed`, `team_om`, `problem_temuan`, `action`, `catatan_tambahan`, `created_at`, `updated_at`) VALUES
(5, 'JOINTING_LURUS', 'DOME 48 Core', 24, '["Rian Suryana", "Budi Santoso"]', 'Kabel FO tertimpa pohon tumbang di tikungan lereng Lembang.', 'Pemasangan closure baru dan perapihan span kabel 200m menjauhi dahan pohon rawan rapuh.', 'Perlu koordinasi perapihan ranting pohon berkala dengan dinas pertamanan setempat.', NOW(), NOW());

INSERT INTO `titik_perbaikan` (`id_tiket`, `nama_titik`, `latitude`, `longitude`, `keterangan`, `created_at`, `updated_at`) VALUES
(5, 'Lereng Hutan Pinus Lembang', -6.82000000, 107.61800000, 'Titik pohon tumbang dan pemasangan closure baru', NOW(), NOW());

-- =============================================================================
-- 8. CONTOH LOG NOTIFIKASI
-- =============================================================================
INSERT INTO `notifikasi_log` (`id_tiket`, `tipe`, `penerima`, `pesan`, `status`, `created_at`, `updated_at`) VALUES
(1, 'WA', '+6281122334455 (Grup NOC & Management)', '🚨 [TIKET CLOSED] BDG-20260920-001\nSegmen: SW BBLU - SW Reog\nStatus: Link UP Normal (MTTR: 5j 15m, SLA: TEPAT)', 'SENT', NOW(), NOW()),
(2, 'INAPP', 'HelpDesk & Teknis Shift', '⚠️ [STOP CLOCK] Tiket BDG-20260924-002 dijeda sementara (Alasan: AKSES_LOKASI).', 'SENT', NOW(), NOW());

-- Aktifkan kembali Foreign Key Checks
SET FOREIGN_KEY_CHECKS = 1;
