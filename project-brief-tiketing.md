# Project Brief: Aplikasi Tiketing Gangguan Backbone
## PT Connecti Jelajah Priangan

---

## 1. Ringkasan Project

Membuat **website aplikasi tiketing gangguan** untuk menggantikan proses manual 
reporting gangguan backbone yang saat ini dilakukan via WhatsApp Group.

Aplikasi ini akan mencatat:
- Open tiket gangguan oleh HelpDesk/NOC
- Update kronologis dari Team Teknis di lapangan
- Perhitungan MTTR & SLA otomatis
- Dokumentasi (foto + koordinat)
- Reporting akhir & closing tiket

---

## 2. Tech Stack (WAJIB)

| Komponen | Teknologi |
|----------|-----------|
| **Frontend** | Bootstrap 5 (HTML, CSS, JS) + Blade Template |
| **Backend** | Laravel 12 (PHP 8.3+) |
| **Database** | MySQL 8.0+ |
| **Authentication** | Laravel Breeze / Fortify (Role-based) |
| **Storage** | Local Storage / AWS S3 (untuk foto) |
| **Maps** | Leaflet.js + OpenStreetMap (gratis) atau Google Maps API |
| **PDF Export** | barryvdh/laravel-dompdf |
| **Excel Export** | maatwebsite/excel |
| **WhatsApp Notif** | Fonnte / Wablas / WhatsApp Cloud API (opsional) |

> ⚠️ **CATATAN**: Website only, **BUKAN mobile app**. Responsive design wajib 
> (mobile-friendly) tapi berbasis web.

---

## 3. User Roles & Hak Akses

| Role | Hak Akses |
|------|-----------|
| **Admin** | Full access, manage user, manage master data |
| **HelpDesk / NOC** | Open tiket, closing tiket, monitoring semua tiket |
| **Team Teknis** | Input kronologis, upload dokumentasi, input resume |
| **SA / Customer Service** | View tiket, update ke client, export report |
| **Client** (opsional) | View status tiket mereka sendiri |

---

## 4. Fitur Utama

### 4.1 Modul Open Tiket (HelpDesk/NOC)
- Form input:
  - Status Link Impact (contoh: "Backbone : SW BBLU - SW Reog Down")
  - Segment / Backbone
  - Tanggal & Jam Awal Gangguan
  - Deskripsi Gangguan
- **Generate Nomor Tiket otomatis** dengan format: `BDG-YYYYMMDD-XXX`
  - Contoh: `BDG-20260914-001`
- Auto-notifikasi ke Team Teknis (WhatsApp / Email / In-App Notification)
- Status awal: `OPEN`

### 4.2 Modul Kronologis (Team Teknis)
- Input update dengan **timestamp otomatis** (jam, nama pengirim, role)
- **Kategori update** (dropdown):
  - `IZIN` - Izin masuk ke ruangan
  - `OTDR` - Hasil pengukuran OTDR
  - `TRACING` - Tracing kabel
  - `MATERIAL` - Material menuju lokasi / sampai lokasi
  - `JOINTING` - Proses jointing / penyambungan
  - `LINK_UP` - Link sudah UP
  - `SELESAI` - Pekerjaan selesai
  - `LAIN` - Update lainnya
- Input informasi (text bebas)
- Upload foto (opsional)
- Input koordinat GPS (opsional, bisa via map picker)
- **Auto-generate header tanggal** jika ada perubahan hari

### 4.3 Modul Resume Pekerjaan
- **Team OM**: input daftar nama teknis (multiple)
- **Problem / Temuan Masalah**: text area (contoh: "Kabel Tertarik Truk di Crossingan Jalan")
- **Action**: text area (contoh: "Jumper Kabel 150 meter, Pemasangan New JC 2 Titik")
- **Material**: table input (nama material, jumlah, satuan)
  - Contoh: JC 24C 2pcs, Kabel 24C 150meter
- **Tagging Titik Perbaikan**: input koordinat JC1, JC2, dst.
  - Bisa via map picker (Leaflet.js)
  - Auto-fill latitude & longitude

### 4.4 Modul MTTR & SLA
- **MTTR** = (Tanggal Close - Tanggal Open) dalam menit/jam
- **SLA Target**: configurable di master data (contoh: 6 jam untuk backbone)
- **Status SLA**:
  - `TEPAT WAKTU` (hijau) jika MTTR <= SLA
  - `MELEBIHI SLA` (merah) jika MTTR > SLA
- **Dashboard MTTR**:
  - Grafik tren MTTR per periode (Chart.js)
  - Rata-rata MTTR per segment/backbone
  - Persentase SLA compliance

### 4.5 Modul Dokumentasi
- Upload foto dengan **timestamp** (otomatis dari sistem)
- Kategori dokumentasi:
  - Hasil Jointing di Closure (JC1, JC2)
  - Closure Terpasang di Tiang (JC1, JC2)
  - Kondisi Lokasi
  - Lain-lain
- Preview geotag di peta (jika ada koordinat)
- Album per tiket

### 4.6 Modul Manuver Core
- Input mapping core sebelum manuver:
  - Contoh: `Tube 2 Core 1 <> Tube 2 Core 1`
- Input mapping core setelah manuver:
  - Contoh: `Tube 2 Core 7 <> Tube 2 Core 7`
- Validasi core banding di KM tertentu
- Catatan action manuver

### 4.7 Modul Reporting
- **Reporting Berkala**: 
  - Auto-generate report per jam / per update
  - Bisa dikirim ke WhatsApp Group / Email
- **Reporting Akhir**:
  - Export PDF (format resume lengkap)
  - Export Excel (untuk analisa data)
- **Closing Tiket**:
  - Hanya HelpDesk/Admin yang bisa closing
  - Validasi: semua field resume harus terisi
  - Auto-set `tanggal_close` & hitung MTTR

### 4.8 Modul Notifikasi (Opsional)
- In-app notification (Laravel Notification)
- WhatsApp notification via API (Fonnte/Wablas)
- Email notification
- Trigger:
  - Tiket baru dibuat → notif ke Teknis
  - Update kronologis → notif ke HelpDesk
  - Tiket closed → notif ke SA/CS & Client

### 4.9 Modul Master Data (Admin)
- Master Segment / Backbone
- Master Material
- Master User & Role
- Master SLA Target per segment
- Master Kategori Update

### 4.10 Dashboard
- **Dashboard HelpDesk**: 
  - Total tiket open/proses/close
  - Tiket yang melebihi SLA (alert merah)
  - List tiket terbaru
- **Dashboard Teknis**:
  - Tiket yang di-assign ke mereka
  - Quick input kronologis
- **Dashboard SA/CS**:
  - Tiket yang perlu di-update ke client
  - Report summary
- **Dashboard Admin**:
  - Statistik keseluruhan
  - MTTR average
  - SLA compliance rate

---

## 5. Skema Database (MySQL)

```sql
-- Tabel Users (bawaan Laravel + custom fields)
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','helpdesk','teknis','sa_cs','client') DEFAULT 'teknis',
    phone VARCHAR(20) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Tabel Tiket
CREATE TABLE tiket (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    no_tiket VARCHAR(20) UNIQUE NOT NULL,  -- BDG-20260914-001
    status_link_impact VARCHAR(150) NOT NULL,
    backbone_segment VARCHAR(100) NOT NULL,
    deskripsi TEXT NULL,
    tanggal_open DATETIME NOT NULL,
    tanggal_close DATETIME NULL,
    status ENUM('OPEN','PROSES','CLOSE') DEFAULT 'OPEN',
    mttr_minutes INT NULL,
    sla_target_minutes INT NULL,
    sla_status ENUM('TEPAT','LEBIH','NA') DEFAULT 'NA',
    created_by BIGINT UNSIGNED,
    closed_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (closed_by) REFERENCES users(id)
);

-- Tabel Kronologis
CREATE TABLE kronologis (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_tiket BIGINT UNSIGNED NOT NULL,
    timestamp DATETIME NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    kategori ENUM('IZIN','OTDR','TRACING','MATERIAL','JOINTING','LINK_UP','SELESAI','LAIN') NOT NULL,
    informasi TEXT NOT NULL,
    foto_url VARCHAR(255) NULL,
    latitude DECIMAL(10,8) NULL,
    longitude DECIMAL(11,8) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_tiket) REFERENCES tiket(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tabel Resume Pekerjaan
CREATE TABLE resume_pekerjaan (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_tiket BIGINT UNSIGNED NOT NULL,
    team_om TEXT NULL,  -- JSON array nama teknis
    problem_temuan TEXT NULL,
    action TEXT NULL,
    catatan_tambahan TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_tiket) REFERENCES tiket(id) ON DELETE CASCADE
);

-- Tabel Material
CREATE TABLE material (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_tiket BIGINT UNSIGNED NOT NULL,
    nama_material VARCHAR(100) NOT NULL,
    jumlah INT NOT NULL,
    satuan VARCHAR(20) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_tiket) REFERENCES tiket(id) ON DELETE CASCADE
);

-- Tabel Titik Perbaikan
CREATE TABLE titik_perbaikan (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_tiket BIGINT UNSIGNED NOT NULL,
    nama_titik VARCHAR(50) NOT NULL,  -- JC1, JC2
    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,
    keterangan VARCHAR(255) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_tiket) REFERENCES tiket(id) ON DELETE CASCADE
);

-- Tabel Manuver Core
CREATE TABLE manuver_core (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_tiket BIGINT UNSIGNED NOT NULL,
    titik VARCHAR(50) NOT NULL,
    core_asal VARCHAR(50) NOT NULL,
    core_tujuan VARCHAR(50) NOT NULL,
    tipe ENUM('SEBELUM','SESUDAH') NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_tiket) REFERENCES tiket(id) ON DELETE CASCADE
);

-- Tabel Dokumentasi
CREATE TABLE dokumentasi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_tiket BIGINT UNSIGNED NOT NULL,
    kategori VARCHAR(100) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    timestamp DATETIME NOT NULL,
    latitude DECIMAL(10,8) NULL,
    longitude DECIMAL(11,8) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (id_tiket) REFERENCES tiket(id) ON DELETE CASCADE
);

-- Tabel Master SLA
CREATE TABLE master_sla (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    backbone_segment VARCHAR(100) NOT NULL,
    sla_target_minutes INT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Tabel Notifikasi Log
CREATE TABLE notifikasi_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_tiket BIGINT UNSIGNED NOT NULL,
    tipe ENUM('WA','EMAIL','INAPP') NOT NULL,
    penerima VARCHAR(100) NOT NULL,
    pesan TEXT NOT NULL,
    status ENUM('SENT','FAILED') NOT NULL,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (id_tiket) REFERENCES tiket(id) ON DELETE CASCADE
);


6. Struktur Folder Laravel
tiketing-gangguan/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   ├── DashboardController.php
│   │   │   ├── TiketController.php
│   │   │   ├── KronologisController.php
│   │   │   ├── ResumeController.php
│   │   │   ├── MaterialController.php
│   │   │   ├── DokumentasiController.php
│   │   │   ├── ManuverCoreController.php
│   │   │   ├── ReportController.php
│   │   │   └── MasterDataController.php
│   │   ├── Middleware/
│   │   │   └── RoleMiddleware.php
│   │   └── Requests/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Tiket.php
│   │   ├── Kronologis.php
│   │   ├── ResumePekerjaan.php
│   │   ├── Material.php
│   │   ├── TitikPerbaikan.php
│   │   ├── ManuverCore.php
│   │   ├── Dokumentasi.php
│   │   ├── MasterSla.php
│   │   └── NotifikasiLog.php
│   ├── Services/
│   │   ├── TiketService.php
│   │   ├── MttrService.php
│   │   ├── NotifikasiService.php
│   │   └── ReportService.php
│   └── Notifications/
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   └── sidebar.blade.php
│   │   ├── dashboard/
│   │   ├── tiket/
│   │   ├── kronologis/
│   │   ├── resume/
│   │   ├── dokumentasi/
│   │   ├── manuver/
│   │   ├── report/
│   │   └── master/
│   ├── css/
│   └── js/
├── routes/
│   └── web.php
├── public/
│   └── uploads/
└── .env

7. Halaman / Route yang Dibutuhkan
Public
GET /login - Halaman login

Authenticated (semua role)
GET /dashboard - Dashboard sesuai role

GET /profile - Profile user

POST /logout

Tiket
GET /tiket - List tiket (filter by status, tanggal, segment)

GET /tiket/create - Form open tiket (HelpDesk/Admin)

POST /tiket - Simpan tiket

GET /tiket/{id} - Detail tiket (kronologis + resume + dokumentasi)

POST /tiket/{id}/close - Closing tiket (HelpDesk/Admin)

GET /tiket/{id}/export-pdf - Export PDF

GET /tiket/{id}/export-excel - Export Excel

Kronologis
POST /tiket/{id}/kronologis - Tambah update kronologis

DELETE /tiket/{id}/kronologis/{id_kronologis} - Hapus (Admin)

Resume
GET /tiket/{id}/resume - Form resume

POST /tiket/{id}/resume - Simpan resume

POST /tiket/{id}/material - Tambah material

POST /tiket/{id}/titik-perbaikan - Tambah titik perbaikan

POST /tiket/{id}/manuver-core - Tambah manuver core

Dokumentasi
POST /tiket/{id}/dokumentasi - Upload foto

DELETE /tiket/{id}/dokumentasi/{id_dok} - Hapus foto

Report
GET /report - Halaman report & statistik

GET /report/mttr - Grafik MTTR

GET /report/sla - SLA compliance

GET /report/export - Export report

Master Data (Admin)
GET /master/segment - CRUD segment

GET /master/material - CRUD material

GET /master/sla - CRUD SLA target

GET /master/user - CRUD user

8. UI/UX Guidelines
Framework CSS: Bootstrap 5.3+

Icons: Bootstrap Icons

Chart: Chart.js

Map: Leaflet.js + OpenStreetMap

Datepicker: Flatpickr / Bootstrap Datepicker

Rich Text: TinyMCE / CKEditor (untuk deskripsi)

Theme: Clean, profesional, warna corporate (biru/teal sesuai logo Connect)

Responsive: Wajib mobile-friendly (tapi tetap web, bukan mobile app)

Sidebar: Fixed sidebar dengan menu sesuai role

Breadcrumb: Di setiap halaman

Toast Notification: Untuk feedback sukses/gagal

Layout Halaman Detail Tiket

┌─────────────────────────────────────────────────────┐
│ Header: No Tiket | Status | MTTR | SLA | Tombol     │
├─────────────────────────────────────────────────────┤
│ Tab: [Kronologis] [Resume] [Dokumentasi] [Manuver]  │
├─────────────────────────────────────────────────────┤
│                                                     │
│  Content sesuai tab yang dipilih                    │
│                                                     │
│  Timeline kronologis (vertical timeline)            │
│  - Jam | Nama | Kategori | Info | Foto | Map        │
│                                                     │
└─────────────────────────────────────────────────────┘

9. Business Logic Penting
9.1 Generate Nomor Tiket
// Format: BDG-YYYYMMDD-XXX
// XXX = urutan tiket hari itu (001, 002, dst)
$prefix = 'BDG-' . date('Ymd') . '-';
$lastTiket = Tiket::where('no_tiket', 'like', $prefix . '%')
                  ->orderBy('no_tiket', 'desc')
                  ->first();
$urutan = $lastTiket ? (int) substr($lastTiket->no_tiket, -3) + 1 : 1;
$noTiket = $prefix . str_pad($urutan, 3, '0', STR_PAD_LEFT);

9.2 Hitung MTTR
// Saat closing tiket
$mttr = $tiket->tanggal_open->diffInMinutes($tiket->tanggal_close);
$tiket->mttr_minutes = $mttr;
$tiket->sla_status = $mttr <= $tiket->sla_target_minutes ? 'TEPAT' : 'LEBIH';

9.3 Auto-header Tanggal di Kronologis
// Di view, kelompokkan kronologis by date
// Tampilkan header tanggal jika berbeda dengan item sebelumnya

9.4 Validasi Closing Tiket
Semua field resume wajib terisi

Minimal 1 dokumentasi foto

Minimal 1 titik perbaikan (jika ada perbaikan)

Status link sudah UP

10. Deliverables
Developer harus menyerahkan:

✅ Source code lengkap (Laravel + Bootstrap)

✅ Database migration & seeder (MySQL)

✅ File .env.example dengan konfigurasi

✅ Dokumentasi instalasi (README.md)

✅ User manual (PDF) untuk setiap role

✅ Akun demo untuk testing (admin, helpdesk, teknis, sa_cs)

✅ Data dummy minimal 10 tiket untuk demo

✅ Deployment guide (VPS / shared hosting)

11. Timeline Pengembangan
Fase	Durasi	Deliverable
Fase 1: Setup & Auth	1 minggu	Laravel + Bootstrap + Auth + Role
Fase 2: Modul Tiket	1.5 minggu	Open/Close tiket + No tiket otomatis
Fase 3: Modul Kronologis	1 minggu	Input update + timeline
Fase 4: Modul Resume & Material	1 minggu	Resume + material + titik perbaikan
Fase 5: Modul Dokumentasi & Manuver	1 minggu	Upload foto + map + manuver core
Fase 6: MTTR/SLA & Reporting	1 minggu	Dashboard + PDF/Excel export
Fase 7: Notifikasi & Master Data	0.5 minggu	WA notif + CRUD master
Fase 8: Testing & Deployment	1 minggu	UAT + deploy + training
Total	~8 minggu	
12. Referensi Dokumen
Dokumen asli: Tiketing Gangguan Via WhatsApp Group.pdf

Tujuan: Kronologis tersusun, MTTR/SLA, update ke client, aktivitas lapangan

Alur: Open Tiket → Isi (koordinasi teknis) → Penutupan (resume + dokumentasi)

Contoh kasus: Gangguan backbone SW BBLU - SW Reog, 14-15 Sept 2026

Material: JC 24C 2pcs, Kabel 24C 150m

Tagging: JC1 (-6.379199, 106.846552), JC2 (-6.379578, 106.846475)

13. Catatan Tambahan
Bahasa interface: Indonesia

Timezone: Asia/Jakarta (WIB)

Format tanggal: DD MMMM YYYY (contoh: 14 September 2026)

Format jam: HH:mm (24 jam)

Multi-user concurrent: Wajib support (minimal 20 user simultan)

Backup database: Auto-backup harian

Log activity: Catat semua aksi user (audit trail)

