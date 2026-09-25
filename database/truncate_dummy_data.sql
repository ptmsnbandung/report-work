-- ═══════════════════════════════════════════════════════════════════════
-- SQL SCRIPT: PEMBERSIHAN DATA TESTING & TRANSAKSI DUMMY
-- Sistem Tiketing Gangguan Backbone - PT MSN
-- ═══════════════════════════════════════════════════════════════════════

-- Nonaktifkan Foreign Key Checks sementara
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Kosongkan Tabel Anak & Relasi Tiket
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

-- 2. Kosongkan Tabel Tiket Utama
DELETE FROM `tiket`;

-- 3. Kosongkan Notifikasi & Log
DELETE FROM `notifications`;
DELETE FROM `notifikasi_log`;

-- 4. Reset Auto Increment ke 1
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

-- Aktifkan kembali Foreign Key Checks
SET FOREIGN_KEY_CHECKS = 1;
