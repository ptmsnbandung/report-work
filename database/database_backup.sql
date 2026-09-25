-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: tiketing_gangguan
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dokumentasi`
--

DROP TABLE IF EXISTS `dokumentasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dokumentasi` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_tiket` bigint(20) unsigned NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `timestamp` datetime NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dokumentasi_timestamp_index` (`timestamp`),
  KEY `idx_dokumentasi_tiket_timestamp` (`id_tiket`,`timestamp`),
  KEY `idx_dokumentasi_tiket_kategori` (`id_tiket`,`kategori`),
  CONSTRAINT `dokumentasi_id_tiket_foreign` FOREIGN KEY (`id_tiket`) REFERENCES `tiket` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dokumentasi`
--

LOCK TABLES `dokumentasi` WRITE;
/*!40000 ALTER TABLE `dokumentasi` DISABLE KEYS */;
/*!40000 ALTER TABLE `dokumentasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kronologis`
--

DROP TABLE IF EXISTS `kronologis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kronologis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_tiket` bigint(20) unsigned NOT NULL,
  `timestamp` datetime NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `kategori` enum('IZIN','OTDR','TRACING','MATERIAL','JOINTING','LINK_UP','SELESAI','LAIN') NOT NULL,
  `informasi` text NOT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kronologis_timestamp_index` (`timestamp`),
  KEY `kronologis_kategori_index` (`kategori`),
  KEY `idx_kronologis_tiket_timestamp` (`id_tiket`,`timestamp`),
  KEY `idx_kronologis_user_created` (`user_id`,`created_at`),
  CONSTRAINT `kronologis_id_tiket_foreign` FOREIGN KEY (`id_tiket`) REFERENCES `tiket` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kronologis_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kronologis`
--

LOCK TABLES `kronologis` WRITE;
/*!40000 ALTER TABLE `kronologis` DISABLE KEYS */;
/*!40000 ALTER TABLE `kronologis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manuver_core`
--

DROP TABLE IF EXISTS `manuver_core`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manuver_core` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_tiket` bigint(20) unsigned NOT NULL,
  `lokasi_tipe` enum('POP','OTB','CLOSURE_LAPANGAN','FAT_FDT','LAINNYA') NOT NULL DEFAULT 'CLOSURE_LAPANGAN',
  `titik` varchar(50) NOT NULL,
  `core_asal` varchar(50) NOT NULL,
  `core_tujuan` varchar(50) NOT NULL,
  `core_dialihkan` varchar(50) DEFAULT NULL COMMENT 'Core cadangan yang digunakan',
  `titik_kembali` varchar(100) DEFAULT NULL COMMENT 'Titik normalisasi jalur',
  `tipe` enum('SEBELUM','SESUDAH') NOT NULL,
  `status_manuver` enum('TEMPORARY','PERMANENT','RESTORED') NOT NULL DEFAULT 'TEMPORARY',
  `status_core_aset` enum('OCCUPIED_MANUVER','BROKEN_LOSS','SPARE_AVAILABLE') NOT NULL DEFAULT 'OCCUPIED_MANUVER',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_manuver_core_tiket_created` (`id_tiket`,`created_at`),
  CONSTRAINT `manuver_core_id_tiket_foreign` FOREIGN KEY (`id_tiket`) REFERENCES `tiket` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manuver_core`
--

LOCK TABLES `manuver_core` WRITE;
/*!40000 ALTER TABLE `manuver_core` DISABLE KEYS */;
/*!40000 ALTER TABLE `manuver_core` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_materials`
--

DROP TABLE IF EXISTS `master_materials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_materials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode_material` varchar(50) NOT NULL,
  `nama_material` varchar(150) NOT NULL,
  `satuan` varchar(50) NOT NULL DEFAULT 'Pcs',
  `stok_tersedia` int(11) NOT NULL DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `master_materials_kode_material_unique` (`kode_material`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_materials`
--

LOCK TABLES `master_materials` WRITE;
/*!40000 ALTER TABLE `master_materials` DISABLE KEYS */;
INSERT INTO `master_materials` VALUES (1,'MAT-FO-01','Kabel FO ADSS 24 Core','Meter',2500,'Kabel FO Aerial ADSS 24 core G.652D',1,'2026-09-25 04:41:27','2026-09-25 04:41:27'),(2,'MAT-FO-02','Kabel FO ADSS 48 Core','Meter',1800,'Kabel FO Aerial ADSS 48 core G.652D',1,'2026-09-25 04:41:27','2026-09-25 04:41:27'),(3,'MAT-CLS-01','Closure Dome 24 Core','Unit',35,'Joint Closure Dome 24 core outdoor IP68',1,'2026-09-25 04:41:27','2026-09-25 04:41:27'),(4,'MAT-CLS-02','Closure Inline 48 Core','Unit',20,'Joint Closure Inline 48 core outdoor IP68',1,'2026-09-25 04:41:27','2026-09-25 04:41:27'),(5,'MAT-PROT-01','Protection Sleeve 60mm','Pcs',500,'Sleeve pelindung sambungan core fusion splicer',1,'2026-09-25 04:41:27','2026-09-25 04:41:27'),(6,'MAT-ACC-01','Suspension Clamp FO','Set',120,'Aksesoris gantungan kabel di tiang',1,'2026-09-25 04:41:27','2026-09-25 04:41:27'),(7,'MAT-ACC-02','Dead End Clamp / Tension Clamp','Set',95,'Klem penarik kabel tiang ujung/sudut',1,'2026-09-25 04:41:27','2026-09-25 04:41:27'),(8,'MAT-ACC-03','Spiral Protection / Wrap Tube','Meter',300,'Pelindung kabel dari gesekan ranting pohon',1,'2026-09-25 04:41:27','2026-09-25 04:41:27');
/*!40000 ALTER TABLE `master_materials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_sla`
--

DROP TABLE IF EXISTS `master_sla`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_sla` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `backbone_segment` varchar(100) NOT NULL,
  `sla_target_minutes` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `master_sla_backbone_segment_unique` (`backbone_segment`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_sla`
--

LOCK TABLES `master_sla` WRITE;
/*!40000 ALTER TABLE `master_sla` DISABLE KEYS */;
INSERT INTO `master_sla` VALUES (1,'SW BBLU - SW Reog',360,'2026-09-25 04:41:27','2026-09-25 04:41:27'),(2,'SW Cikutra - SW Dago',360,'2026-09-25 04:41:27','2026-09-25 04:41:27'),(3,'SW Soekarno Hatta - SW Buah Batu',360,'2026-09-25 04:41:27','2026-09-25 04:41:27'),(4,'SW Pasteur - SW Sukajadi',240,'2026-09-25 04:41:27','2026-09-25 04:41:27'),(5,'SW Kopo - SW Cibaduyut',180,'2026-09-25 04:41:27','2026-09-25 04:41:27'),(6,'SW Ujungberung - SW Cibiru',180,'2026-09-25 04:41:27','2026-09-25 04:41:27'),(7,'SW Cimahi - SW Padalarang',240,'2026-09-25 04:41:27','2026-09-25 04:41:27'),(8,'SW Setiabudi - SW Lembang',300,'2026-09-25 04:41:27','2026-09-25 04:41:27');
/*!40000 ALTER TABLE `master_sla` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `material`
--

DROP TABLE IF EXISTS `material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `material` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_tiket` bigint(20) unsigned NOT NULL,
  `nama_material` varchar(100) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `satuan` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_material_tiket_created` (`id_tiket`,`created_at`),
  CONSTRAINT `material_id_tiket_foreign` FOREIGN KEY (`id_tiket`) REFERENCES `tiket` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material`
--

LOCK TABLES `material` WRITE;
/*!40000 ALTER TABLE `material` DISABLE KEYS */;
/*!40000 ALTER TABLE `material` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_09_18_000001_create_tiket_table',1),(5,'2026_09_18_000002_create_kronologis_table',1),(6,'2026_09_18_000003_create_resume_pekerjaan_table',1),(7,'2026_09_18_000004_create_material_table',1),(8,'2026_09_18_000005_create_titik_perbaikan_table',1),(9,'2026_09_18_000006_create_manuver_core_table',1),(10,'2026_09_18_000007_create_dokumentasi_table',1),(11,'2026_09_18_000008_create_master_sla_table',1),(12,'2026_09_18_000009_create_notifikasi_log_table',1),(13,'2026_09_21_172205_create_notifications_table',1),(14,'2026_09_21_172210_create_master_materials_table',1),(15,'2026_09_22_000001_add_avatar_to_users_table',1),(16,'2026_09_23_000001_remove_client_role_from_users_table',1),(17,'2026_09_23_000002_create_push_subscriptions_table',1),(18,'2026_09_23_000003_add_performance_indexes_table',1),(19,'2026_09_24_000001_add_fase1_workflow_features_to_tiket_table',1),(20,'2026_09_24_000002_create_joint_closures_and_enhance_manuver_core_table',1),(21,'2026_09_24_000003_add_loss_db_to_tiket_joint_closure_cores_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifikasi_log`
--

DROP TABLE IF EXISTS `notifikasi_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifikasi_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_tiket` bigint(20) unsigned NOT NULL,
  `tipe` enum('WA','EMAIL','INAPP') NOT NULL,
  `penerima` varchar(100) NOT NULL,
  `pesan` text NOT NULL,
  `status` enum('SENT','FAILED') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifikasi_log_tipe_index` (`tipe`),
  KEY `notifikasi_log_status_index` (`status`),
  KEY `idx_notifikasi_log_tiket_created` (`id_tiket`,`created_at`),
  CONSTRAINT `notifikasi_log_id_tiket_foreign` FOREIGN KEY (`id_tiket`) REFERENCES `tiket` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifikasi_log`
--

LOCK TABLES `notifikasi_log` WRITE;
/*!40000 ALTER TABLE `notifikasi_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifikasi_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `push_subscriptions`
--

DROP TABLE IF EXISTS `push_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `push_subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `endpoint` varchar(500) NOT NULL,
  `public_key` varchar(255) DEFAULT NULL,
  `auth_token` varchar(255) DEFAULT NULL,
  `content_encoding` varchar(20) NOT NULL DEFAULT 'aesgcm',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `push_subscriptions_endpoint_unique` (`endpoint`),
  KEY `push_subscriptions_user_id_index` (`user_id`),
  CONSTRAINT `push_subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `push_subscriptions`
--

LOCK TABLES `push_subscriptions` WRITE;
/*!40000 ALTER TABLE `push_subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `push_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resume_pekerjaan`
--

DROP TABLE IF EXISTS `resume_pekerjaan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resume_pekerjaan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_tiket` bigint(20) unsigned NOT NULL,
  `tipe_penanganan` varchar(50) DEFAULT NULL,
  `joint_closure_type` varchar(100) DEFAULT NULL,
  `core_count_jointed` int(11) DEFAULT NULL,
  `team_om` text DEFAULT NULL,
  `problem_temuan` text DEFAULT NULL,
  `action` text DEFAULT NULL,
  `catatan_tambahan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `resume_pekerjaan_id_tiket_foreign` (`id_tiket`),
  CONSTRAINT `resume_pekerjaan_id_tiket_foreign` FOREIGN KEY (`id_tiket`) REFERENCES `tiket` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resume_pekerjaan`
--

LOCK TABLES `resume_pekerjaan` WRITE;
/*!40000 ALTER TABLE `resume_pekerjaan` DISABLE KEYS */;
/*!40000 ALTER TABLE `resume_pekerjaan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tiket`
--

DROP TABLE IF EXISTS `tiket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tiket` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `no_tiket` varchar(20) NOT NULL,
  `status_link_impact` varchar(150) NOT NULL,
  `backbone_segment` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `closing_notes_teknisi` text DEFAULT NULL,
  `tipe_penanganan` varchar(50) NOT NULL DEFAULT 'JOINTING_LURUS',
  `tanggal_open` datetime NOT NULL,
  `first_response_at` datetime DEFAULT NULL,
  `response_time_minutes` int(11) DEFAULT NULL,
  `tanggal_close` datetime DEFAULT NULL,
  `resolved_at` datetime DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'OPEN',
  `mttr_minutes` int(11) DEFAULT NULL,
  `total_stop_clock_minutes` int(11) NOT NULL DEFAULT 0,
  `is_stop_clock` tinyint(1) NOT NULL DEFAULT 0,
  `sla_target_minutes` int(11) DEFAULT NULL,
  `sla_status` enum('TEPAT','LEBIH','NA') NOT NULL DEFAULT 'NA',
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `resolved_by` bigint(20) unsigned DEFAULT NULL,
  `closed_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tiket_no_tiket_unique` (`no_tiket`),
  KEY `tiket_backbone_segment_index` (`backbone_segment`),
  KEY `tiket_tanggal_open_index` (`tanggal_open`),
  KEY `tiket_tanggal_close_index` (`tanggal_close`),
  KEY `tiket_status_index` (`status`),
  KEY `tiket_sla_status_index` (`sla_status`),
  KEY `idx_tiket_status_tanggal_open` (`status`,`tanggal_open`),
  KEY `idx_tiket_segment_status` (`backbone_segment`,`status`),
  KEY `idx_tiket_created_by` (`created_by`),
  KEY `idx_tiket_closed_by` (`closed_by`),
  KEY `tiket_resolved_by_foreign` (`resolved_by`),
  KEY `tiket_resolved_at_index` (`resolved_at`),
  KEY `tiket_is_stop_clock_index` (`is_stop_clock`),
  CONSTRAINT `tiket_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tiket_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tiket_resolved_by_foreign` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tiket`
--

LOCK TABLES `tiket` WRITE;
/*!40000 ALTER TABLE `tiket` DISABLE KEYS */;
/*!40000 ALTER TABLE `tiket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tiket_handover_shifts`
--

DROP TABLE IF EXISTS `tiket_handover_shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tiket_handover_shifts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_tiket` bigint(20) unsigned NOT NULL,
  `shift_from` varchar(50) NOT NULL,
  `shift_to` varchar(50) NOT NULL,
  `user_from_id` bigint(20) unsigned DEFAULT NULL,
  `user_to_id` bigint(20) unsigned DEFAULT NULL,
  `catatan_handover` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tiket_handover_shifts_id_tiket_foreign` (`id_tiket`),
  KEY `tiket_handover_shifts_user_from_id_foreign` (`user_from_id`),
  KEY `tiket_handover_shifts_user_to_id_foreign` (`user_to_id`),
  CONSTRAINT `tiket_handover_shifts_id_tiket_foreign` FOREIGN KEY (`id_tiket`) REFERENCES `tiket` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tiket_handover_shifts_user_from_id_foreign` FOREIGN KEY (`user_from_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tiket_handover_shifts_user_to_id_foreign` FOREIGN KEY (`user_to_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tiket_handover_shifts`
--

LOCK TABLES `tiket_handover_shifts` WRITE;
/*!40000 ALTER TABLE `tiket_handover_shifts` DISABLE KEYS */;
/*!40000 ALTER TABLE `tiket_handover_shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tiket_joint_closure_cores`
--

DROP TABLE IF EXISTS `tiket_joint_closure_cores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tiket_joint_closure_cores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_joint_closure` bigint(20) unsigned NOT NULL,
  `tube_asal` varchar(30) NOT NULL COMMENT 'Nama/No Tube Asal',
  `core_asal` varchar(30) NOT NULL COMMENT 'Nama/No Core Asal',
  `tube_tujuan` varchar(30) DEFAULT NULL COMMENT 'Nama/No Tube Jumper',
  `core_tujuan` varchar(30) DEFAULT NULL COMMENT 'Nama/No Core Jumper',
  `status_core` enum('TERHUBUNG','SPARE','LOSS_PUTUS','MANUVER') NOT NULL DEFAULT 'TERHUBUNG',
  `loss_db` decimal(5,2) DEFAULT NULL COMMENT 'Redaman sambungan dB',
  `keterangan` varchar(255) DEFAULT NULL COMMENT 'Alokasi link/keterangan port',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tiket_joint_closure_cores_id_joint_closure_status_core_index` (`id_joint_closure`,`status_core`),
  CONSTRAINT `tiket_joint_closure_cores_id_joint_closure_foreign` FOREIGN KEY (`id_joint_closure`) REFERENCES `tiket_joint_closures` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tiket_joint_closure_cores`
--

LOCK TABLES `tiket_joint_closure_cores` WRITE;
/*!40000 ALTER TABLE `tiket_joint_closure_cores` DISABLE KEYS */;
/*!40000 ALTER TABLE `tiket_joint_closure_cores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tiket_joint_closures`
--

DROP TABLE IF EXISTS `tiket_joint_closures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tiket_joint_closures` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_tiket` bigint(20) unsigned NOT NULL,
  `nama_closure` varchar(100) NOT NULL COMMENT 'Nama/Kode Closure, misal JC-BBLU-04A',
  `tipe_closure` enum('DOME','INLINE','BOX_FAT','OTB','CLOSURE_STANDAR') NOT NULL DEFAULT 'DOME',
  `status_aset` enum('EKSISTING','ASET_BARU') NOT NULL DEFAULT 'EKSISTING' COMMENT 'ASET_BARU jika ada pemotongan & closure baru',
  `kapasitas_kabel_asal` smallint(5) unsigned NOT NULL DEFAULT 96 COMMENT '2, 12, 24, 48, 96, 144, 288 Core',
  `jumlah_tube_asal` smallint(5) unsigned NOT NULL DEFAULT 8,
  `kapasitas_kabel_jumper` smallint(5) unsigned NOT NULL DEFAULT 48 COMMENT '2, 12, 24, 48, 96, 144, 288 Core',
  `jumlah_tube_jumper` smallint(5) unsigned NOT NULL DEFAULT 4,
  `jenis_sambungan` enum('LURUS_STRAIGHT','PERCABANGAN_BRANCH','LOOP_MANUVER') NOT NULL DEFAULT 'LURUS_STRAIGHT',
  `lokasi_penempatan` varchar(150) DEFAULT NULL COMMENT 'No Tiang, Manhole, Handhole',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tiket_joint_closures_created_by_foreign` (`created_by`),
  KEY `tiket_joint_closures_id_tiket_status_aset_index` (`id_tiket`,`status_aset`),
  CONSTRAINT `tiket_joint_closures_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tiket_joint_closures_id_tiket_foreign` FOREIGN KEY (`id_tiket`) REFERENCES `tiket` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tiket_joint_closures`
--

LOCK TABLES `tiket_joint_closures` WRITE;
/*!40000 ALTER TABLE `tiket_joint_closures` DISABLE KEYS */;
/*!40000 ALTER TABLE `tiket_joint_closures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tiket_stop_clocks`
--

DROP TABLE IF EXISTS `tiket_stop_clocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tiket_stop_clocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_tiket` bigint(20) unsigned NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 0,
  `alasan_kategori` varchar(100) NOT NULL,
  `alasan_detail` text DEFAULT NULL,
  `requested_by` bigint(20) unsigned DEFAULT NULL,
  `stopped_by` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tiket_stop_clocks_id_tiket_foreign` (`id_tiket`),
  KEY `tiket_stop_clocks_requested_by_foreign` (`requested_by`),
  KEY `tiket_stop_clocks_stopped_by_foreign` (`stopped_by`),
  KEY `tiket_stop_clocks_start_time_index` (`start_time`),
  KEY `tiket_stop_clocks_end_time_index` (`end_time`),
  KEY `tiket_stop_clocks_is_active_index` (`is_active`),
  CONSTRAINT `tiket_stop_clocks_id_tiket_foreign` FOREIGN KEY (`id_tiket`) REFERENCES `tiket` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tiket_stop_clocks_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tiket_stop_clocks_stopped_by_foreign` FOREIGN KEY (`stopped_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tiket_stop_clocks`
--

LOCK TABLES `tiket_stop_clocks` WRITE;
/*!40000 ALTER TABLE `tiket_stop_clocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `tiket_stop_clocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `titik_perbaikan`
--

DROP TABLE IF EXISTS `titik_perbaikan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `titik_perbaikan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_tiket` bigint(20) unsigned NOT NULL,
  `nama_titik` varchar(50) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_titik_perbaikan_tiket_created` (`id_tiket`,`created_at`),
  CONSTRAINT `titik_perbaikan_id_tiket_foreign` FOREIGN KEY (`id_tiket`) REFERENCES `tiket` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `titik_perbaikan`
--

LOCK TABLES `titik_perbaikan` WRITE;
/*!40000 ALTER TABLE `titik_perbaikan` DISABLE KEYS */;
/*!40000 ALTER TABLE `titik_perbaikan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','helpdesk','teknis','sa_cs') NOT NULL DEFAULT 'teknis',
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator NOC','admin@connecti.id',NULL,'$2y$12$Z5t/n60ZPVjA92m2HaoX4e8avtmTHuf4yW30IzaQquD60wExgsRtW','admin','081122334455',NULL,1,NULL,'2026-09-25 04:41:27','2026-09-25 04:41:27'),(2,'NOC HelpDesk & SA/CS Operator','helpdesk@connecti.id',NULL,'$2y$12$HDrO1r7iD9pyPjkOnR1GRuvEZUaGw3OmKoh5RGRK8pPv6TX1RqtfC','helpdesk','081233445566',NULL,1,NULL,'2026-09-25 04:41:27','2026-09-25 04:41:27'),(3,'Rian Suryana (Teknis)','teknis@connecti.id',NULL,'$2y$12$9DwDjsL1McCv4OBnIhoiiebaQVnlBpH8fsHO0ixf1Eeq/kRokymPC','teknis','081344556677',NULL,1,NULL,'2026-09-25 04:41:27','2026-09-25 04:41:27'),(4,'Budi Santoso (Teknis)','teknis2@connecti.id',NULL,'$2y$12$xz1s1.f6EAto1CvkAUXikOdM1dhq2vWxYVcuKEOxbR791iF7U/uaa','teknis','081388990011',NULL,1,NULL,'2026-09-25 04:41:27','2026-09-25 04:41:27'),(5,'Sarah Agustina (CS/SA)','sacs@connecti.id',NULL,'$2y$12$PhXizyDKqflOxKdLc6Gzy.5YI0GhlG1Fa7rxNKDR9ha0kBafd0u6S','sa_cs','081455667788',NULL,1,NULL,'2026-09-25 04:41:27','2026-09-25 04:41:27');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-25 11:43:30
