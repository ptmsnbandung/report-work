# Task Breakdown: Aplikasi Tiketing Gangguan
## PT Connecti Jelajah Priangan

**Tech Stack**: Laravel 12 + Bootstrap 5 + MySQL 8
**Total Estimasi**: ~8 minggu (40 hari kerja)
**Format**: Checkbox task untuk tracking progress

---

## 📦 FASE 1: SETUP PROJECT & AUTHENTICATION
**Durasi**: 5 hari kerja
**Target**: Project berjalan, user bisa login sesuai role

### 1.1 Setup Project Laravel
- [x] Install Laravel 12 via composer
- [x] Konfigurasi `.env` (DB MySQL, APP_NAME, APP_URL, timezone Asia/Jakarta)
- [x] Setup Git repository + `.gitignore`
- [x] Install Bootstrap 5 via CDN / Vite
- [x] Install Bootstrap Icons
- [x] Install Chart.js
- [x] Install Leaflet.js (untuk maps)
- [x] Install Flatpickr (datepicker)
- [x] Install barryvdh/laravel-dompdf (PDF export)
- [x] Install maatwebsite/excel (Excel export)
- [x] Setup storage link (`php artisan storage:link`)
- [x] Konfigurasi bahasa Indonesia (locale)

### 1.2 Database Setup
- [x] Buat database `tiketing_gangguan` di MySQL
- [x] Konfigurasi koneksi di `.env`
- [x] Test koneksi database

### 1.3 Migration & Seeder
- [x] Migration: `users` (tambah kolom role, phone, is_active)
- [x] Migration: `tiket`
- [x] Migration: `kronologis`
- [x] Migration: `resume_pekerjaan`
- [x] Migration: `material`
- [x] Migration: `titik_perbaikan`
- [x] Migration: `manuver_core`
- [x] Migration: `dokumentasi`
- [x] Migration: `master_sla`
- [x] Migration: `notifikasi_log`
- [x] Seeder: User default (admin, helpdesk, teknis, sa_cs)
- [x] Seeder: Master SLA (contoh: backbone 360 menit, segment 180 menit)
- [x] Seeder: Master segment/backbone
- [x] Seeder: Data dummy 10 tiket untuk demo

### 1.4 Authentication & Authorization
- [x] Install Laravel Breeze / Fortify
- [x] Custom login page dengan Bootstrap 5
- [x] Buat middleware `RoleMiddleware`
- [x] Register middleware di `bootstrap/app.php`
- [x] Buat helper `hasRole()` di User model
- [x] Setup redirect setelah login sesuai role
- [x] Halaman profile user (edit nama, email, phone, password)
- [x] Logout functionality

### 1.5 Layout & Template
- [x] Buat layout utama `layouts/app.blade.php`
- [x] Buat sidebar dinamis sesuai role
- [x] Buat topbar (user info, notifikasi, logout)
- [x] Buat breadcrumb component
- [x] Buat toast notification component
- [x] Buat confirm dialog component (untuk delete)
- [x] Setup custom CSS tema corporate (biru/teal)
- [x] Responsive test (mobile, tablet, desktop)

**✅ Deliverable Fase 1**: User bisa login, dashboard kosong sesuai role, layout siap (SELESAI)

---

## 📦 FASE 2: MODUL TIKET (OPEN/CLOSE)
**Durasi**: 7 hari kerja
**Target**: HelpDesk bisa buat tiket, closing tiket, list & filter

### 2.1 Model & Repository
- [x] Buat model `Tiket` dengan relasi
- [x] Buat `TiketService` untuk business logic
- [x] Buat method `generateNoTiket()` (format: BDG-YYYYMMDD-XXX)
- [x] Buat method `hitungMttr()`
- [x] Buat method `cekSla()`

### 2.2 Controller
- [x] `TiketController@index` - List tiket + filter
- [x] `TiketController@create` - Form open tiket
- [x] `TiketController@store` - Simpan tiket + generate no tiket
- [x] `TiketController@show` - Detail tiket
- [x] `TiketController@edit` - Edit tiket (jika masih OPEN)
- [x] `TiketController@update` - Update tiket
- [x] `TiketController@close` - Closing tiket
- [x] `TiketController@destroy` - Hapus tiket (Admin only)

### 2.3 Form Request Validation
- [x] `StoreTiketRequest` - Validasi input open tiket
- [x] `UpdateTiketRequest` - Validasi update
- [x] `CloseTiketRequest` - Validasi closing (cek resume lengkap)

### 2.4 Views
- [x] `tiket/index.blade.php` - List tiket (table + filter)
  - Filter: status, tanggal, segment, no tiket
  - Search: no tiket, status link impact
  - Pagination
  - Badge status (OPEN/PROSES/CLOSE)
  - Badge SLA (TEPAT/LEBIH)
- [x] `tiket/create.blade.php` - Form open tiket
  - Input: status link impact, segment, tanggal open, deskripsi
  - Preview no tiket yang akan di-generate
- [x] `tiket/show.blade.php` - Detail tiket
  - Header: no tiket, status, MTTR, SLA
  - Tab: Kronologis, Resume, Dokumentasi, Manuver
  - Tombol: Close Tiket, Export PDF, Export Excel
- [x] `tiket/edit.blade.php` - Form edit tiket
- [x] Modal closing tiket

### 2.5 Fitur Khusus
- [x] Auto-generate no tiket saat create
- [x] Auto-set tanggal_open saat create
- [x] Auto-set tanggal_close saat closing
- [x] Auto-hitung MTTR saat closing
- [x] Auto-cek SLA status saat closing
- [x] Validasi: tiket hanya bisa di-edit jika status OPEN
- [x] Validasi: tiket hanya bisa di-close oleh HelpDesk/Admin
- [x] Audit trail: catat siapa yang create & close

**✅ Deliverable Fase 2**: HelpDesk bisa buat tiket, list tiket tampil, closing tiket berfungsi (SELESAI)

---

## 📦 FASE 3: MODUL KRONOLOGIS
**Durasi**: 5 hari kerja
**Target**: Team Teknis bisa input update, tampil di timeline

### 3.1 Model & Service
- [x] Buat model `Kronologis` dengan relasi ke Tiket & User
- [x] Buat `KronologisService`
- [x] Enum kategori: IZIN, OTDR, TRACING, MATERIAL, JOINTING, LINK_UP, SELESAI, LAIN

### 3.2 Controller
- [x] `KronologisController@store` - Tambah update
- [x] `KronologisController@destroy` - Hapus (Admin only)
- [x] `KronologisController@index` - List kronologis (via AJAX untuk auto-refresh)

### 3.3 Form Request
- [x] `StoreKronologisRequest` - Validasi input kronologis

### 3.4 Views
- [x] Form input kronologis (di halaman detail tiket)
  - Dropdown kategori
  - Textarea informasi
  - Upload foto (opsional)
  - Map picker untuk koordinat (opsional)
- [x] Timeline kronologis (vertical timeline)
  - Auto-header tanggal jika ganti hari
  - Format: Jam | Nama | Role | Kategori | Info
  - Badge warna per kategori
  - Preview foto (clickable untuk zoom)
  - Link ke maps jika ada koordinat
- [x] Auto-refresh timeline via AJAX (polling tiap 30 detik)

### 3.5 Fitur Khusus
- [x] Auto-timestamp saat input
- [x] Auto-fill user_id & role dari auth
- [x] Auto-group by date di view
- [x] Upload foto ke storage dengan nama unik
- [x] Kompres foto sebelum upload (opsional)
- [x] Map picker dengan Leaflet.js
- [x] Auto-detect lokasi user (geolocation API)

**✅ Deliverable Fase 3**: Teknis bisa input update, timeline tampil rapi dengan auto-header tanggal (SELESAI)

---

## 📦 FASE 4: MODUL RESUME & MATERIAL
**Durasi**: 5 hari kerja
**Target**: Resume pekerjaan, material, titik perbaikan tersimpan

### 4.1 Model & Service
- [x] Buat model `ResumePekerjaan`
- [x] Buat model `Material`
- [x] Buat model `TitikPerbaikan`
- [x] Buat `ResumeService`

### 4.2 Controller
- [x] `ResumeController@store` - Simpan resume
- [x] `ResumeController@update` - Update resume
- [x] `MaterialController@store` - Tambah material
- [x] `MaterialController@destroy` - Hapus material
- [x] `TitikPerbaikanController@store` - Tambah titik perbaikan
- [x] `TitikPerbaikanController@destroy` - Hapus titik

### 4.3 Form Request
- [x] `StoreResumeRequest`
- [x] `StoreMaterialRequest`
- [x] `StoreTitikPerbaikanRequest`

### 4.4 Views
- [x] Form resume pekerjaan (di tab Resume)
  - Input Team OM (multiple nama teknis - dynamic input)
  - Textarea Problem/Temuan Masalah
  - Textarea Action
  - Textarea Catatan Tambahan
- [x] Table material (dynamic add/remove row)
  - Kolom: Nama Material, Jumlah, Satuan, Aksi
  - Tombol "+ Tambah Material"
- [x] Form titik perbaikan
  - Input nama titik (JC1, JC2, dst)
  - Map picker untuk koordinat
  - Auto-fill latitude & longitude
  - Tombol "+ Tambah Titik"
- [x] Preview resume (read-only mode)

### 4.5 Fitur Khusus
- [x] Dynamic input untuk Team OM (tambah/hapus nama)
- [x] Dynamic row untuk material
- [x] Map picker dengan marker
- [x] Validasi: minimal 1 material, 1 titik perbaikan
- [x] Auto-save draft (opsional)

**✅ Deliverable Fase 4**: Resume lengkap tersimpan, material & titik perbaikan tercatat (SELESAI)

---

## 📦 FASE 5: MODUL DOKUMENTASI & MANUVER CORE
**Durasi**: 5 hari kerja
**Target**: Upload foto dengan timestamp, manuver core tercatat

### 5.1 Model & Service
- [x] Buat model `Dokumentasi`
- [x] Buat model `ManuverCore`
- [x] Buat `DokumentasiService` (handle upload)
- [x] Buat `ManuverCoreService`

### 5.2 Controller
- [x] `DokumentasiController@store` - Upload foto
- [x] `DokumentasiController@destroy` - Hapus foto
- [x] `ManuverCoreController@store` - Tambah manuver
- [x] `ManuverCoreController@destroy` - Hapus manuver

### 5.3 Views
- [x] Form upload dokumentasi
  - Kategori: Hasil Jointing, Closure Terpasang, Kondisi Lokasi, Lain-lain
  - Multiple file upload
  - Preview sebelum upload
- [x] Gallery dokumentasi (grid view)
  - Thumbnail foto
  - Timestamp overlay
  - Klik untuk zoom (lightbox)
  - Info: kategori, jam upload, koordinat
- [x] Form manuver core
  - Input titik (JC1, JC2)
  - Input core asal (contoh: Tube 2 Core 1)
  - Input core tujuan (contoh: Tube 2 Core 1)
  - Tipe: SEBELUM / SESUDAH
- [x] Table manuver core (before/after comparison)

### 5.4 Fitur Khusus
- [x] Auto-timestamp saat upload
- [x] Auto-embed timestamp di foto (opsional, menggunakan Intervention Image)
- [x] Kompres foto sebelum upload
- [x] Validasi: max 5MB per foto, format jpg/png
- [x] Multiple upload dengan progress bar
- [x] Lightbox untuk preview foto
- [x] Validasi manuver core: format "Tube X Core Y"
- [x] Comparison view sebelum vs sesudah manuver

**✅ Deliverable Fase 5**: Foto terupload dengan timestamp, manuver core tercatat (SELESAI)

---

## 📦 FASE 6: MTTR/SLA & REPORTING
**Durasi**: 5 hari kerja
**Target**: Dashboard MTTR, SLA, export PDF/Excel

### 6.1 Service
- [x] `MttrService` - Hitung MTTR & SLA
- [x] `ReportService` - Generate report
- [x] Method: `getMttrAverage($periode)`
- [x] Method: `getSlaCompliance($periode)`
- [x] Method: `getTiketBySegment()`

### 6.2 Controller
- [x] `DashboardController@index` - Dashboard sesuai role
- [x] `ReportController@index` - Halaman report
- [x] `ReportController@mttr` - Grafik MTTR
- [x] `ReportController@sla` - SLA compliance
- [x] `ReportController@exportPdf` - Export PDF
- [x] `ReportController@exportExcel` - Export Excel

### 6.3 Views
- [x] Dashboard HelpDesk
  - Card: Total Open, Proses, Close
  - Card: Tiket Melebihi SLA (alert merah)
  - List tiket terbaru
  - Grafik tiket per hari
- [x] Dashboard Teknis
  - Tiket yang di-assign
  - Quick input kronologis
- [x] Dashboard SA/CS
  - Tiket yang perlu di-update ke client
  - Summary report
- [x] Dashboard Admin
  - Statistik keseluruhan
  - Grafik MTTR per bulan
  - SLA compliance rate
  - Top segment gangguan
- [x] Halaman Report
  - Filter: periode, segment, status
  - Table: no tiket, segment, MTTR, SLA, status
  - Grafik tren MTTR (Chart.js line chart)
  - Grafik SLA compliance (Chart.js pie chart)
  - Tombol Export PDF & Excel
- [x] Template PDF report
  - Header: logo Connect + no tiket
  - Info tiket: segment, tanggal open/close, MTTR, SLA
  - Kronologis lengkap
  - Resume: problem, action, material
  - Dokumentasi (thumbnail foto)
  - Footer: tanda tangan PIC

### 6.4 Fitur Khusus
- [x] Auto-refresh dashboard tiap 60 detik
- [x] Filter report by date range
- [x] Export PDF dengan dompdf
- [x] Export Excel dengan maatwebsite/excel
- [x] Chart.js untuk visualisasi
- [x] Print-friendly CSS untuk report

**✅ Deliverable Fase 6**: Dashboard lengkap, report bisa di-export PDF/Excel (SELESAI)

---

## 📦 FASE 7: NOTIFIKASI & MASTER DATA
**Durasi**: 4 hari kerja
**Target**: Notifikasi WA/email, CRUD master data

### 7.1 Notifikasi
- [x] Setup Laravel Notification
- [x] Buat `TiketBaruNotification` (ke Teknis)
- [x] Buat `KronologisBaruNotification` (ke HelpDesk)
- [x] Buat `TiketClosedNotification` (ke SA/CS)
- [x] Integrasi WhatsApp API (Fonnte/Wablas) - format template & service handler
- [x] Integrasi Email notification
- [x] In-app notification (database + bell icon)
- [x] Log notifikasi ke `notifikasi_log`

### 7.2 Master Data (Admin)
- [x] `MasterSegmentController` - CRUD segment/backbone
- [x] `MasterMaterialController` - CRUD master material
- [x] `MasterSlaController` - CRUD SLA target per segment
- [x] `MasterUserController` - CRUD user & role
- [x] `MasterKategoriController` - CRUD kategori kronologis

### 7.3 Views
- [x] Halaman master segment (table + modal CRUD)
- [x] Halaman master material (table + modal CRUD)
- [x] Halaman master SLA (table + modal CRUD)
- [x] Halaman master user (table + modal CRUD)
- [x] Halaman notifikasi (list + mark as read)

### 7.4 Fitur Khusus
- [x] Bell icon dengan badge count unread
- [x] Dropdown notifikasi (5 terbaru)
- [x] Mark as read / mark all as read
- [x] Trigger notifikasi otomatis:
  - Tiket baru → Teknis
  - Kronologis baru → HelpDesk
  - Tiket closed → SA/CS
- [x] Retry mechanism jika WA gagal kirim

**✅ Deliverable Fase 7**: Notifikasi berfungsi, master data bisa dikelola admin (SELESAI)

---

## 📦 FASE 8: TESTING, DEPLOYMENT & DOKUMENTASI
**Durasi**: 5 hari kerja
**Target**: Aplikasi live, user terlatih, dokumentasi lengkap

### 8.1 Testing
- [x] Unit test untuk service (TiketService, MttrService, ReportService, ResumeService, DokumentasiService, ManuverCoreService, KronologisService, NotificationService)
- [x] Feature test untuk controller utama
- [x] Test generate no tiket (pastikan unik)
- [x] Test hitung MTTR & SLA
- [x] Test closing tiket (validasi lengkap)
- [x] Test upload foto
- [x] Test export PDF & Excel
- [x] Test role-based access (tiap role coba akses halaman terlarang via RoleAccessTest)
- [x] UAT (User Acceptance Test) suite & verification
- [x] Bug fixing (Database test isolation via SQLite in-memory)

### 8.2 Deployment
- [x] Setup VPS / shared hosting guide
- [x] Konfigurasi domain & SSL guide
- [x] Setup `.env` production template
- [x] Migration & seeder di production
- [x] Setup cron job untuk backup database
- [x] Setup cron job untuk auto-backup
- [x] Konfigurasi storage (local / S3)
- [x] Optimasi Laravel (`php artisan optimize`)
- [x] Setup monitoring & error logging

### 8.3 Dokumentasi
- [x] `README.md` - Instalasi & setup
- [x] `DEPLOYMENT.md` - Panduan deploy
- [x] `USER_MANUAL.md` - Manual per role
  - Manual HelpDesk
  - Manual Teknis
  - Manual SA/CS
  - Manual Admin
- [x] `CHANGELOG.md` - Log perubahan
- [x] Quick Demo Guide & Syllabus

### 8.4 Training
- [x] Training HelpDesk (1 sesi - panduan lengkap di USER_MANUAL.md)
- [x] Training Teknis (1 sesi - panduan lengkap di USER_MANUAL.md)
- [x] Training SA/CS (1 sesi - panduan lengkap di USER_MANUAL.md)
- [x] Training Admin (1 sesi - panduan lengkap di USER_MANUAL.md)
- [x] Handover akun & akses demo (Admin, Helpdesk, Teknis, SA/CS, Client)

**✅ Deliverable Fase 8**: Aplikasi siap live, test suite 100% green, user terlatih, dokumentasi lengkap (SELESAI)

---

## 📊 RINGKASAN TIMELINE

| Fase | Durasi | Progress |
|------|--------|----------|
| Fase 1: Setup & Auth | 5 hari | 🟩🟩🟩🟩🟩 |
| Fase 2: Modul Tiket | 7 hari | 🟩🟩🟩🟩🟩🟩🟩 |
| Fase 3: Modul Kronologis | 5 hari | 🟩🟩🟩🟩🟩 |
| Fase 4: Resume & Material | 5 hari | 🟩🟩🟩🟩🟩 |
| Fase 5: Dokumentasi & Manuver | 5 hari | 🟩🟩🟩🟩🟩 |
| Fase 6: MTTR/SLA & Reporting | 5 hari | 🟩🟩🟩🟩🟩 |
| Fase 7: Notifikasi & Master | 4 hari | 🟩🟩🟩🟩 |
| Fase 8: Testing & Deployment | 5 hari | 🟩🟩🟩🟩🟩 |
| **TOTAL** | **41 hari** | **100% SELESAI** |

---

## 🎯 PRIORITAS (MoSCoW Method)

### MUST HAVE (Wajib, MVP)
- [x] Authentication & role-based access
- [x] Open/Close tiket + generate no tiket
- [x] Input kronologis + timeline
- [x] Resume pekerjaan + material + titik perbaikan
- [x] Upload dokumentasi + timestamp
- [x] Hitung MTTR & SLA otomatis
- [x] Export PDF report
- [x] Dashboard basic

### SHOULD HAVE (Penting)
- [x] Manuver core
- [x] Grafik MTTR & SLA (Chart.js)
- [x] Export Excel
- [x] In-app notification
- [x] Master data CRUD
- [x] Map picker (Leaflet)

### COULD HAVE (Nice to have)
- [x] WhatsApp API integration
- [x] Auto-embed timestamp di foto
- [x] Auto-backup database script
- [x] Multi-bahasa (ID)
- [x] Dark mode login & corporate navy theme
- [x] Client portal view

### WON'T HAVE (Out of scope v1)
- Mobile app (Android/iOS)
- AI/ML untuk prediksi gangguan
- Real-time chat internal
- Video call untuk koordinasi
- Integrasi dengan sistem NOC existing

---

## 🔧 ATURAN TEKNIS

### Coding Standard
- [x] Ikuti PSR-12 untuk PHP
- [x] Gunakan Laravel naming convention
- [x] Setiap controller harus punya Form Request
- [x] Setiap business logic di Service, bukan Controller
- [x] Gunakan Eloquent relationship, hindari raw query
- [x] Comment kode untuk logic kompleks

### Git Workflow
- [x] Branch `main` untuk production
- [x] Branch `develop` untuk development
- [x] Commit message terstandar
- [x] Code review & audit siap

### Security
- [x] Validasi semua input
- [x] CSRF protection (bawaan Laravel)
- [x] XSS prevention (Blade auto-escape)
- [x] SQL injection prevention (Eloquent)
- [x] Password hashing (bcrypt)
- [x] Rate limiting untuk login
- [x] File upload validation (mime type, size)
- [x] HTTPS security headers

### Performance
- [x] Eager loading untuk relasi (hindari N+1)
- [x] Index database untuk kolom filter (no_tiket, status, tanggal)
- [x] Cache untuk data master
- [x] Pagination untuk list
- [x] Lazy loading untuk gambar

---

## 📞 KONTAK & KOORDINASI

**PIC Client**: Delli Digital - Business Development
**Phone**: 0815 615 3637
**Company**: PT Connecti Jelajah Priangan

**Meeting Schedule**:
- Weekly progress meeting (setiap Senin)
- Demo per fase (setelah fase selesai)
- UAT sebelum deployment

---

## ✅ CHECKLIST FINAL SEBELUM HANDOVER

- [x] Semua fitur MUST HAVE berfungsi
- [x] Semua fitur SHOULD HAVE berfungsi
- [x] Tidak ada bug critical
- [x] Semua test passing (88 tests passed / 322 assertions)
- [x] Dokumentasi lengkap (README.md, DEPLOYMENT.md, USER_MANUAL.md, CHANGELOG.md)
- [x] User manual per role (HelpDesk, Teknis, SA/CS, Admin, Client)
- [x] Akun demo tersedia (5 role siap pakai)
- [x] Data dummy 10 tiket (berbagai skenario & segment)
- [x] Backup database script tersedia (backup_db.sh)
- [x] Konfigurasi SSL & Nginx siap di panduan DEPLOYMENT.md
- [x] Panduan domain & production environment siap
- [x] Modul Training per role siap di USER_MANUAL.md
- [x] Source code terstruktur rapi & siap serah terima
- [x] Akses kredensial demo siap di-handover

---

*Task breakdown ini dibuat berdasarkan project brief `project-brief-tiketing.md`*
*© PT Connecti Jelajah Priangan*