-- ==============================================================================
-- SQL MIGRATION & INSERT DUMP: AKUN NOC & TEKNIK DARI APLIKASI SEBELUMNYA
-- Target Database : MSN Work Report
-- Tanggal         : 27 September 2026
-- Filter          : Hanya Divisi 'NOC' dan 'Teknik'
-- Kolom Diambil   : id, name, username, email, password, role, phone, is_active
-- Kolom Dibuang   : no_rekening, bank, atas_nama, kode_karyawan, lokasi, divisi
-- ==============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Tambahkan kolom username ke tabel users jika belum ada
ALTER TABLE `users` 
  ADD COLUMN IF NOT EXISTS `username` VARCHAR(100) NULL AFTER `name`;

-- Pastikan indeks unique untuk username
SET @exist := (SELECT COUNT(*) FROM information_schema.statistics 
               WHERE table_schema = DATABASE() 
                 AND table_name = 'users' 
                 AND index_name = 'users_username_unique');
SET @sqlstmt := IF(@exist > 0, 'SELECT 1', 'ALTER TABLE `users` ADD UNIQUE KEY `users_username_unique` (`username`)');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. Beri username default untuk 5 user awal sistem
UPDATE `users` SET `username` = 'admin' WHERE `id` = 1 AND `username` IS NULL;
UPDATE `users` SET `username` = 'helpdesk' WHERE `id` = 2 AND `username` IS NULL;
UPDATE `users` SET `username` = 'teknis' WHERE `id` = 3 AND `username` IS NULL;
UPDATE `users` SET `username` = 'teknis2' WHERE `id` = 4 AND `username` IS NULL;
UPDATE `users` SET `username` = 'sacs' WHERE `id` = 5 AND `username` IS NULL;

-- 3. Data User Akun NOC & Teknik
INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `role`, `phone`, `is_active`, `created_at`, `updated_at`) VALUES
(29, 'CHRISTIAN PERDI', 'Chriss', 'chrissferdy12@gmail.com', '$2y$10$hfC63BLfm85Mg3Zgroewyeb.EJbH5Nl6fSjpt.t6NHx8Lqh.wJ3J.', 'teknis', '087829724169', 1, NOW(), NOW()),
(30, 'Ipin Aripin', 'Ipin Aripin', 'ipinaripin219@gmail.com', '$2y$10$m.WeRH0yLl3JP/.CgLwiKOHeRyUKbeXEruIATeL3mW/UFSXIek2vO', 'teknis', '089684193712', 1, NOW(), NOW()),
(31, 'HARRY SETIONO', 'harry estes', 'harryestes@msn.net.id', '$2y$10$3fuefKm9O0banRyZTTLZ5eTXIFhDz5OViQfzlZ9qp5h27cPMUnNQ.', 'helpdesk', '089630595394', 1, NOW(), NOW()),
(33, 'DUDI DURAHMAN', 'Dudi Durahman', 'dudidurahman@msn.net.id', '$2y$10$K6gc6tojrusYJd9XbNQRiObJJok1kVg26SDIpGzzlRzgqJVL8Krnm', 'teknis', '089655394872', 1, NOW(), NOW()),
(34, 'PADILAH M NUR', 'Padilah M Nur', 'padilmuhammad53116@gamil.com', '$2y$10$h1QD0Aaka.M.1FPwISDeuuos6sqoynQermGzXH2wSPinJc1Nbm9VO', 'teknis', '083190225326', 1, NOW(), NOW()),
(37, 'RICKY SAHARA PUTRA', 'rickysp', 'rickyputraa03@gmail.com', '$2y$10$3C0TzHKsJKDShNtjwZfx1.O4bmpRNHFET06LIXLtLr.iBF.3zTjRi', 'helpdesk', '085721183196', 1, NOW(), NOW()),
(39, 'DENI HAMDANI', 'Deni hamdani', 'denihamdeni1974@gmail.com', '$2y$10$r3jZaGOBLzKujeXlYGuUzeH.P2k97mBsJrlAKIELkGwXvp/r4kAWG', 'teknis', '083113147404', 1, NOW(), NOW()),
(42, 'IYAN SOFIAN', 'Iyan Sofian', 'jeunuchian@gmail.com', '$2y$10$I8P6vwI6aI1BQuAtWbX5IuwXz.KDg9JfGHcFEfyStKEBR9cZ5K1vC', 'teknis', '081285347696', 1, NOW(), NOW()),
(44, 'BAGUS JOKO PRIYONO', 'Bagus Joko', 'bagusjoko@msn.net.id', '$2y$10$nYO10GUWKb1KIRCsITDwQOXp4QZBr5p0EO6EJQOCIhqazNNM5OwF6', 'admin', NULL, 1, NOW(), NOW()),
(45, 'DODI SODIKIN', 'Dodi Sodikin', 'dodisodikin@msn.net.id', '$2y$10$FLwPsWdEMxpqVAwRKBn6J.D1l84eWDxMLK509O51yMhYiNUW9Aasi', 'teknis', '089652270447', 1, NOW(), NOW()),
(47, 'ENCU SAMSUDIN', 'Encu samsudin', 'encu.samsudin.2525@gmail.com', '$2y$10$sUwP77WxSw7UVu56DWSDh.ilSJVhG5WpsnUYPUw7BOVls4K0T3T0m', 'teknis', '082115051760', 1, NOW(), NOW()),
(48, 'DIKA IBNU', 'Dika ibnu', 'dikabmx5@gmail.com', '$2y$10$IysdXlYPskmaW.yEQyBP5O2cKl.yQ.FVyZKhOYT90w7/Lapg/s0di', 'teknis', '082115051760', 1, NOW(), NOW()),
(49, 'KELVIN SULTAN', 'Kelvin sultan', 'kelvinsultan3@gmail.com', '$2y$10$bJh52VC0DveQ2p4kQNxfvORLB3SjBy2F4SZt6p/uUm0Th4qMzWcsm', 'helpdesk', '089529951898', 1, NOW(), NOW()),
(51, 'MUHAMAD RAFI RAMDANI', 'mrafiramdhani', 'rafiahmat272018@gmail.com', '$2y$10$Bfsx6eePt6oXHvVatKLfjOUsw381ntMExRKkS1lEXlUfl3qR8ILCy', 'helpdesk', '082117138236', 1, NOW(), NOW()),
(52, 'LEVANDRI AHMAD F AJMAL', 'Levandri Ajmal', 'levandriajmal@gmail.com', '$2y$10$KkYPDKvcPBIyEZkXWqWcUu/yTFf6G.l8LwIm1uhrqFXgOC3OBDDc6', 'admin', NULL, 1, NOW(), NOW()),
(55, 'RYAN SEPTIADI', 'Ryan Gondel', 'ryangondel@msn.net.id', '$2y$10$MiD.mgVdgZcTbE3ps0b9S.hsi9ROOQtaIbDTKoGrvsqqf2xqUUR1m', 'teknis', '083831228235', 1, NOW(), NOW()),
(56, 'DEDE RAHMAN', 'Dede Rahman', 'bettafisht21@gmail.com', '$2y$10$./csFGy.SXd5dznPYrHpeeFw3d2NbJ/0JXJeZXmkQNLY0pS1OG1/K', 'teknis', '081320222935', 1, NOW(), NOW()),
(59, 'BUDI SAMSUDIN', 'Budi samsudin', 'budisamsudin321@gmail.com', '$2y$10$dY6gKrgAT5MupPw/cA5HLOgqivBpj9OTtRugPJPNNg5hKRvW1s44a', 'teknis', '08977547887', 1, NOW(), NOW()),
(60, 'LEVANDRI AHMAD F AJMAL', 'Ajmal', 'ajmal@msn.net.id', '$2y$10$t7S0VtAHK/rI39RQA0djg.NXV7wM71ubW1O0I0DrhgDV5VkHfqpay', 'helpdesk', '082216491002', 1, NOW(), NOW()),
(61, 'BAGUS JOKO PRIYONO', 'Bagus', 'bagus@msn.net.id', '$2y$10$6ccQEtJWXdFc9TiR0eTzQ.6MhCad620yV2GCdTbnPQ9mPfzRpsVQ.', 'helpdesk', '089656952045', 1, NOW(), NOW()),
(62, 'IPIN ARIPIN', 'Ipin', 'ipin@msn.net.id', '$2y$10$gBKiSEDJ5AQa65eJLBKGMeYFdtjyuYOfbtftucaGCvjvB8R9L7KGq', 'teknis', '089684193712', 1, NOW(), NOW()),
(64, 'SANDY WAHYUDY', 'Sandy Wahyudi', 'sandywahyudi@msn.net.id', '$2y$10$jt0LadlDPjekIn.BB8Z.sOGNV1nHDMuyFeH7TxTXMD1jBujHZ7I0G', 'teknis', '089665719400', 1, NOW(), NOW()),
(65, 'DANDI ALRIZKI', 'Dandi Alrizki', 'dandialrizki@msn.net.id', '$2y$10$64JF9mEHOKoUBVME3oHr3e0Xkmn1Wbg1GQPrnHDomdQdn6hKl/pJ2', 'teknis', '088971352212', 1, NOW(), NOW()),
(66, 'FENDRADIKA', 'Fendradika', 'fendradika@msn.net.id', '$2y$10$TDnpjaVqNI7t3vTk4sYKO.FcneOE6wYumGkN/rbJuriKIo17Ie3hy', 'teknis', '087735081997', 1, NOW(), NOW()),
(67, 'REZA APRIANT', 'Reza Apriant', 'rezaapriant@msn.net.id', '$2y$10$JUwxGrp0PeMa/ogDz1k40uurwx/XnCL0k5LM31SXdvPtzCsyZwJFm', 'teknis', '083155750975', 1, NOW(), NOW()),
(68, 'ABDUL GHANI', 'Abdul Ghani', 'abdulghani@msn.net.id', '$2y$10$lXl3KzTiC76jVN2Iqk42Ceyaat15Aweyps6eaFy2ZTIcHsASSTV7W', 'teknis', '085624213955', 1, NOW(), NOW()),
(74, 'Iwan', 'Iwan', 'iwan@msn.net.id', '$2y$10$B5RD/MG51Y9.r2gkWgMg7e7NtAWCcY6/IVxUV1kcjQ1t6WXyzB3lC', 'helpdesk', NULL, 1, NOW(), NOW()),
(81, 'MUHAMMAD HASYA NUR RASHIF', 'Nur Rashif', 'nurrashif@msn.net.id', '$2y$10$ztYIxWJPIfwc3v1AzqLAs.QGDUMhvdIdiOoEd6S0viH4kfSOH9wSe', 'helpdesk', NULL, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`),
  `username` = VALUES(`username`),
  `email` = VALUES(`email`),
  `password` = VALUES(`password`),
  `role` = VALUES(`role`),
  `phone` = VALUES(`phone`),
  `is_active` = VALUES(`is_active`),
  `updated_at` = NOW();

SET FOREIGN_KEY_CHECKS = 1;
