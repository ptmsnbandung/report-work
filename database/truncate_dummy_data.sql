-- ═══════════════════════════════════════════════════════════════════════
-- SQL SCRIPT: PEMBERSIHAN DATA TESTING & TRANSAKSI DUMMY
-- Sistem Tiketing Gangguan Backbone - PT MSN
-- ═══════════════════════════════════════════════════════════════════════

-- Nonaktifkan Foreign Key Checks sementara untuk TRUNCATE
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Kosongkan Tabel Anak & Relasi Tiket
TRUNCATE TABLE `tiket_joint_closure_cores`;
TRUNCATE TABLE `tiket_joint_closures`;
TRUNCATE TABLE `manuver_cores`;
TRUNCATE TABLE `dokumentasis`;
TRUNCATE TABLE `titik_perbaikans`;
TRUNCATE TABLE `materials`;
TRUNCATE TABLE `resume_pekerjaans`;
TRUNCATE TABLE `kronologis`;

-- 2. Kosongkan Tabel Tiket Utama
TRUNCATE TABLE `tikets`;

-- 3. Kosongkan Notifikasi & Log
TRUNCATE TABLE `notifications`;
TRUNCATE TABLE `notifikasi_logs`;

-- 4. (Opsional) Reset Auto Increment Tiket ke 1
ALTER TABLE `tikets` AUTO_INCREMENT = 1;
ALTER TABLE `kronologis` AUTO_INCREMENT = 1;
ALTER TABLE `resume_pekerjaans` AUTO_INCREMENT = 1;
ALTER TABLE `materials` AUTO_INCREMENT = 1;
ALTER TABLE `titik_perbaikans` AUTO_INCREMENT = 1;
ALTER TABLE `manuver_cores` AUTO_INCREMENT = 1;
ALTER TABLE `dokumentasis` AUTO_INCREMENT = 1;
ALTER TABLE `tiket_joint_closures` AUTO_INCREMENT = 1;
ALTER TABLE `tiket_joint_closure_cores` AUTO_INCREMENT = 1;

-- Aktifkan kembali Foreign Key Checks
SET FOREIGN_KEY_CHECKS = 1;
