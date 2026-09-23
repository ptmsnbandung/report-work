# 🚀 Sistem Tiketing Gangguan Backbone Network
### PT Connecti Jelajah Priangan (CJP)

Platform terpusat pengelolaan, monitoring, dan pelaporan insiden gangguan jaringan backbone fiber optic terintegrasi secara realtime, berbasis **Laravel 12**, **Bootstrap 5**, **MySQL 8**, **Leaflet.js**, **Chart.js**, **DomPDF**, dan **Maatwebsite Excel**.

---

## 📑 Daftar Isi
- [Fitur Utama](#-fitur-utama)
- [Hak Akses & Role Matrix](#-hak-akses--role-matrix)
- [Kebutuhan Sistem (Prerequisites)](#-kebutuhan-sistem)
- [Panduan Instalasi & Setup Lokal](#-panduan-instalasi--setup-lokal)
- [Akun Demo Pengujian](#-akun-demo-pengujian)
- [Automated Testing](#-automated-testing)
- [Struktur Direktori Proyek](#-struktur-direktori-proyek)
- [Dokumentasi Tambahan](#-dokumentasi-tambahan)

---

## 🌟 Fitur Utama

1. **Manajemen Tiket Gangguan (Open / Close)**
   - Auto-generate Nomor Tiket unik standar: `BDG-YYYYMMDD-XXX`.
   - Status tracking: `OPEN` &rarr; `PROSES` &rarr; `CLOSE`.
   - Perhitungan otomatis MTTR (*Mean Time to Recovery*) dalam hitungan menit & jam.
   - Evaluasi kepatuhan SLA (*Service Level Agreement*) otomatis (`TEPAT` atau `LEBIH`).
   - Alert visual badge merah jika tiket aktif melampaui batas SLA.

2. **Modul Kronologis Lapangan (Timeline Realtime)**
   - Input update kronologis progress lapangan dengan kategori: `IZIN`, `OTDR`, `TRACING`, `MATERIAL`, `JOINTING`, `LINK_UP`, `SELESAI`, `LAIN`.
   - Upload foto bukti penanganan dengan geolocation GPS & map picker Leaflet.
   - Auto-grouping berdasarkan tanggal kejadian & auto-refresh polling via AJAX setiap 30 detik.

3. **Modul Resume Pekerjaan, Material & Titik Perbaikan**
   - Pencatatan Team OM lapangan (multi-teknis).
   - Analisa Problem / Temuan Masalah, Action perbaikan, dan Catatan Rekomendasi.
   - Tabel pemakaian material (nama material, jumlah, satuan).
   - Tagging titik perbaikan (JC1, JC2, dsb) dengan koordinat Latitude & Longitude terintegrasi peta Leaflet & Google Maps.

4. **Modul Dokumentasi Foto & Manuver Core**
   - Multi-photo upload dropzone dengan preview instan, kompresi, timestamp, dan lightbox preview.
   - Pencatatan manuver core fiber optic (Before vs After comparison: contoh `Tube 2 Core 1` &rarr; `Tube 2 Core 7`).

5. **Monitoring MTTR/SLA & Laporan (PDF & Excel)**
   - Executive dashboard role-based dengan visualisasi tren bulanan MTTR (Line Chart) dan SLA Compliance Rate (Doughnut Chart).
   - Export Berita Acara Tunggal format **PDF A4 Portrait** resmi dengan kop PT Connecti Jelajah Priangan dan tanda tangan 3 pihak (HelpDesk, OM Leader, Manager).
   - Export Rekap Laporan Bulanan **PDF A4 Landscape** & spreadsheet **Excel (.xlsx)**.

6. **Notifikasi Multi-Channel & Master Data Management**
   - In-app notification dengan badge counter unread di navbar (polling otomatis 30 detik).
   - Otomasi notifikasi: Tiket Baru &rarr; Tim Teknis; Update Kronologis &rarr; HelpDesk; Tiket Closed &rarr; SA/CS & Client.
   - WhatsApp Notification Service (template corporate ready-to-use).
   - Master Data CRUD untuk Admin: Segment Backbone, Master Material, Target SLA, dan Manajemen Pengguna (CRUD, Toggle Status Aktif, Reset Password).

---

## 👥 Hak Akses & Role Matrix

| Fitur / Halaman | Admin NOC | HelpDesk NOC | Tim Teknis | SA / CS | Client |
| :--- | :---: | :---: | :---: | :---: | :---: |
| **Dashboard Metrik** | ✅ (Semua) | ✅ (Operasional) | ✅ (Tugas Saya) | ✅ (Status Pelanggan) | ✅ (Ringkasan) |
| **Open Tiket Baru** | ✅ | ✅ | ❌ | ❌ | ❌ |
| **Daftar & Detail Tiket** | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Edit Tiket (Status OPEN)** | ✅ | ✅ | ❌ | ❌ | ❌ |
| **Closing Tiket** | ✅ | ✅ | ❌ | ❌ | ❌ |
| **Input Kronologis** | ✅ | ✅ | ✅ | ❌ | ❌ |
| **Input Resume & Material** | ✅ | ✅ | ✅ | ❌ | ❌ |
| **Upload Dokumentasi Foto** | ✅ | ✅ | ✅ | ❌ | ❌ |
| **Input Manuver Core** | ✅ | ✅ | ✅ | ❌ | ❌ |
| **Export PDF & Excel** | ✅ | ✅ | ✅ | ✅ | ❌ |
| **Pusat Notifikasi** | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Master Data & User Management** | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Hapus Tiket / Data** | ✅ | ❌ | ❌ | ❌ | ❌ |

---

## 💻 Kebutuhan Sistem

- **PHP**: >= 8.2 (ekstensi: `pdo_mysql`, `pdo_sqlite`, `mbstring`, `openssl`, `curl`, `fileinfo`, `gd`)
- **Web Server**: Apache / Nginx
- **Database**: MySQL 8.0+ / MariaDB 10.4+
- **Composer**: >= 2.x
- **Node.js & NPM**: >= 18.x

---

## ⚙️ Panduan Instalasi & Setup Lokal

1. **Clone repository & masuk ke direktori proyek**:
   ```bash
   cd c:\xampp\htdocs\work_report
   ```

2. **Install dependensi PHP & Composer**:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment**:
   Salin file `.env.example` menjadi `.env` jika belum ada:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Database di `.env`**:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=tiketing_gangguan
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Jalankan Migrasi & Database Seeder**:
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Buat Symlink Storage**:
   ```bash
   php artisan storage:link
   ```

7. **Jalankan Development Server**:
   ```bash
   php artisan serve --port=8000
   ```
   Akses aplikasi pada browser di: **`http://127.0.0.1:8000`**

---

## 🔑 Akun Demo Pengujian

Semua akun demo menggunakan password default: **`password`**

| Role | Nama | Email | Password |
| :--- | :--- | :--- | :--- |
| **Admin NOC** | Administrator NOC | `admin@connecti.id` | `password` |
| **HelpDesk NOC** | NOC HelpDesk Operator | `helpdesk@connecti.id` | `password` |
| **Team Teknis** | Rian Suryana | `teknis@connecti.id` | `password` |
| **Team Teknis 2** | Budi Santoso | `teknis2@connecti.id` | `password` |
| **SA / CS** | Sarah Agustina | `sacs@connecti.id` | `password` |
| **Client** | PT Mitra Sejahtera | `client@connecti.id` | `password` |

*(Tersedia tombol **Quick Demo Login** di halaman login untuk kemudahan login 1-klik).*

---

## 🧪 Automated Testing

Aplikasi dilengkapi test suite komprehensif (Unit Test & Feature Test) menggunakan **PHPUnit** dan SQLite in-memory database:

```bash
# Jalankan seluruh test suite
php artisan test

# Jalankan pengujian modul tertentu
php artisan test --filter=RoleAccessTest
php artisan test --filter=TiketTest
php artisan test --filter=MttrServiceTest
php artisan test --filter=ReportTest
php artisan test --filter=MasterDataTest
```

**Status Pengujian**: `88 passed / 322 assertions (100% Green)`.

---

## 📁 Struktur Direktori Proyek

```
work_report/
├── app/
│   ├── Exports/            # Maatwebsite Excel export class (TiketExport)
│   ├── Http/
│   │   ├── Controllers/    # Controllers (Tiket, Kronologis, Resume, Report, Master, dll)
│   │   ├── Middleware/     # RoleMiddleware & Authentication checks
│   │   └── Requests/       # Form Request validations
│   ├── Models/             # Eloquent Models & relationships
│   ├── Notifications/      # Laravel Notification classes (In-App & Email)
│   └── Services/           # Business Logic Layer (Tiket, MTTR, Report, Resume, Notifikasi, WA)
├── database/
│   ├── migrations/         # Skema database MySQL
│   └── seeders/            # Database seeders data awal & 10 dummy tickets
├── resources/
│   └── views/              # Blade UI views (Corporate navy/teal styling)
│       ├── auth/           # Login form
│       ├── dashboard/      # Role-based KPI dashboard
│       ├── master/         # Master data CRUD views (Segment, Material, SLA, User)
│       ├── notifications/  # Notification center
│       ├── reports/        # Analytics & PDF templates
│       └── tiket/          # Ticket index, create, edit & 5-tab detail view
├── tests/                  # Unit & Feature automated tests
├── DEPLOYMENT.md           # Panduan Deployment Server Production
├── USER_MANUAL.md          # Panduan Penggunaan per Role
└── CHANGELOG.md            # Log Perubahan Versi
```

---

## 📚 Dokumentasi Tambahan

- 📘 [Panduan Deployment Production (DEPLOYMENT.md)](./DEPLOYMENT.md)
- 📖 [Panduan Pengguna / User Manual per Role (USER_MANUAL.md)](./USER_MANUAL.md)
- 📝 [Log Perubahan & Riwayat Versi (CHANGELOG.md)](./CHANGELOG.md)

---
*© 2026 PT Connecti Jelajah Priangan. All rights reserved.*
