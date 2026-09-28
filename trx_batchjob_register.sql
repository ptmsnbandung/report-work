/*
 Navicat Premium Dump SQL

 Source Server         : Msn_Connection
 Source Server Type    : MySQL
 Source Server Version : 50742 (5.7.42-0ubuntu0.18.04.1)
 Source Host           : 103.161.207.5:3306
 Source Schema         : ims_v3

 Target Server Type    : MySQL
 Target Server Version : 50742 (5.7.42-0ubuntu0.18.04.1)
 File Encoding         : 65001

 Date: 28/09/2026 15:17:52
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for trx_batchjob_register
-- ----------------------------
DROP TABLE IF EXISTS `trx_batchjob_register`;
CREATE TABLE `trx_batchjob_register`  (
  `nomor_internet` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nik_penduduk` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `nama_pelanggan` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `rt_pasang` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `rw_pasang` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `nomor_bangunan` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `alamat_pasang` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `kode_wilayah_kelurahan_pasang` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `jenis_bangunan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `lon_lat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `loc_maps` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `note_request` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `kode_bandwith` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `kode_pop` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `ont_us` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `ont_ps` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status_reg` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `media_akses` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `ppn` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `ppn_nom` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `potongan` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `potongan_note` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `last_month_billing` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `last_year_billing` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `periode_billing` int(2) NULL DEFAULT NULL,
  `jns_notif` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `is_termin` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `is_suspend` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `count_suspend` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `is_denda` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `islock` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `prorate` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `date_create` datetime NULL DEFAULT NULL,
  `user_create` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `date_update` datetime NULL DEFAULT NULL,
  `user_update` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `hide` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `mitra` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `group_layanan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `nama_sales` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `olt` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `index_olt` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`nomor_internet`) USING BTREE,
  UNIQUE INDEX `nomor_internet[trx_batchjob_register]`(`nomor_internet`) USING BTREE,
  INDEX `hide[trx_batchjob_register]`(`hide`) USING BTREE,
  INDEX `fk_trx_batchjob_register_m_pop_1`(`kode_pop`) USING BTREE,
  INDEX `fk_trx_batchjob_register_m_status_registrasi_1`(`status_reg`) USING BTREE,
  INDEX `fk_trx_batchjob_register_m_pelanggan_1`(`nik_penduduk`) USING BTREE,
  INDEX `fk_trx_batchjob_register_m_wilayah_2`(`kode_wilayah_kelurahan_pasang`) USING BTREE,
  INDEX `fk_trx_batchjob_register_m_bandwith_1`(`kode_bandwith`) USING BTREE,
  INDEX `nama_pelanggan[trx_bj_regis]`(`nama_pelanggan`) USING BTREE,
  INDEX `last_year_billing[trx_bj_reg]`(`last_year_billing`) USING BTREE,
  INDEX `last_month_billing[trx_bj_reg]`(`last_month_billing`) USING BTREE,
  CONSTRAINT `fk_trx_batchjob_register_m_bandwith_1` FOREIGN KEY (`kode_bandwith`) REFERENCES `m_bandwith` (`kode_bandwith`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_trx_batchjob_register_m_pelanggan_1` FOREIGN KEY (`nik_penduduk`) REFERENCES `m_pelanggan` (`nik_penduduk`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_trx_batchjob_register_m_pop_1` FOREIGN KEY (`kode_pop`) REFERENCES `m_pop` (`kode_pop`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_trx_batchjob_register_m_status_hide_1` FOREIGN KEY (`hide`) REFERENCES `m_status_hide` (`hide`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_trx_batchjob_register_m_status_registrasi_1` FOREIGN KEY (`status_reg`) REFERENCES `m_status_registrasi` (`status_reg`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_trx_batchjob_register_m_wilayah_2` FOREIGN KEY (`kode_wilayah_kelurahan_pasang`) REFERENCES `m_wilayah` (`kode_wilayah_kelurahan`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
