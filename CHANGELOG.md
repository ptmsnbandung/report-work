# 📜 Catatan Rilis & Perubahan (CHANGELOG)
### Sistem Tiketing Gangguan Backbone - PT Connecti Jelajah Priangan

Semua perubahan besar, penambahan fitur, dan perbaikan dari setiap milestone fase proyek didokumentasikan di bawah ini.

---

## [1.0.0] - 2026-09-21 - FASE 8: TESTING, DEPLOYMENT & DOKUMENTASI FINAL

### ✨ Added
- Test Suite Terpadu (Unit Test & Feature Test) mencakup seluruh service, controller, middleware, dan skenario otorisasi role dengan **88 automated tests passing (322 assertions)**.
- `RoleAccessTest` untuk verifikasi matriks otorisasi 5 role pengguna (`admin`, `helpdesk`, `teknis`, `sa_cs`, `client`) dan proteksi guest.
- `README.md` komprehensif dengan petunjuk setup, arsitektur, fitur, dan akun demo.
- `DEPLOYMENT.md` panduan deployment production lengkap (Nginx, MySQL, SSL Certbot, Supervisor queue worker, cron scheduler, dan skrip auto-backup harian).
- `USER_MANUAL.md` buku panduan operasional per role (HelpDesk, Tim Teknis, SA/CS, Admin, Client).
- Skrip cadangan database MySQL terkompresi `backup_db.sh`.
- Final checklist audit sebelum serah terima (handover).

---

## [0.7.0] - 2026-09-21 - FASE 7: NOTIFIKASI & MASTER DATA

### ✨ Added
- **Multi-Channel Notification System**:
  - `TiketBaruNotification` untuk notifikasi instan tiket baru ke Tim Teknis & Admin.
  - `KronologisBaruNotification` untuk notifikasi update lapangan ke HelpDesk & Admin.
  - `TiketClosedNotification` untuk notifikasi perbaikan selesai ke SA/CS & Client.
- `WhatsAppService` terintegrasi dengan template format pesan WhatsApp resmi.
- `NotificationService` dengan logging audit ke tabel `notifikasi_log` (`INAPP`, `WA`, `EMAIL`).
- Dynamic Notification Bell di navbar topbar dengan AJAX polling realtime setiap 30 detik dan badge counter unread.
- Halaman Pusat Notifikasi (`/notifications`) dengan filter status, tombol tandai dibaca, dan direct link ke tiket.
- **Master Data CRUD (Admin Only)**:
  - Master Segment Backbone (`/master/segments`).
  - Master Material & Peralatan (`/master/materials`) beserta seeder standar fiber optic.
  - Master Target SLA per Segment (`/master/sla`).
  - Master Manajemen Pengguna (`/master/users`) dengan fitur Toggle Aktif/Nonaktif akun dan Reset Password oleh Admin.

---

## [0.6.0] - 2026-09-21 - FASE 6: MTTR/SLA & REPORTING

### ✨ Added
- `MttrService` untuk kalkulasi durasi MTTR dalam hitungan menit/jam, tren bulanan, dan evaluasi SLA (`TEPAT` / `LEBIH`).
- `ReportService` untuk analitik data agregat dan generator dokumen PDF & Excel.
- Halaman Analitik & Monitoring MTTR/SLA (`/reports`) dengan 6 KPI Cards, visualisasi tren bulanan Chart.js Line Chart, dan SLA Compliance Rate Doughnut Chart.
- Export PDF Berita Acara Tunggal format **A4 Portrait** resmi dengan kop PT Connecti Jelajah Priangan, SLA metrics, resume, material, titik perbaikan, kronologis lengkap, dokumentasi foto, dan tanda tangan 3 pihak (HelpDesk, OM Leader, Manager).
- Export PDF Rekap Ringkasan **A4 Landscape**.
- Export Rekapitulasi Data Spreadsheet **Excel (.xlsx)** menggunakan `Maatwebsite\Excel` (`TiketExport`).
- Role-specific dashboard widgets pada `DashboardController` dan alert banner merah jika terdapat tiket aktif melampaui batas SLA.

---

## [0.5.0] - 2026-09-21 - FASE 5: MODUL DOKUMENTASI & MANUVER CORE

### ✨ Added
- `DokumentasiService` & `DokumentasiController` untuk multi-photo dropzone upload dengan kompresi, overlay timestamp, dan storage aman.
- Galeri foto dokumentasi dengan kategori (Hasil Jointing, Closure Terpasang, Kondisi Lokasi) dan lightbox modal zoom.
- `ManuverCoreService` & `ManuverCoreController` untuk pencatatan alur core fiber optic (Before vs After comparison view).
- Auto-transition status tiket dari `OPEN` ke `PROSES` saat tim teknis mengunggah dokumentasi pertama.

---

## [0.4.0] - 2026-09-21 - FASE 4: MODUL RESUME & MATERIAL

### ✨ Added
- `ResumeService` & `ResumeController` untuk pengelolaan data resume pekerjaan (Team OM dinamis, Problem/Temuan, Action, Catatan Rekomendasi).
- `MaterialController` & tabel dinamis pencatatan material terpakai (kabel FO, closure, sleeve, clamp).
- `TitikPerbaikanController` dengan penanda koordinat GPS dan integrasi Leaflet Map Picker.
- Tab navigasi modern di halaman detail tiket.

---

## [0.3.0] - 2026-09-21 - FASE 3: MODUL KRONOLOGIS TIMELINE

### ✨ Added
- `KronologisService` & `KronologisController` untuk pencatatan timeline penanganan gangguan bertahap (`IZIN`, `OTDR`, `TRACING`, `MATERIAL`, `JOINTING`, `LINK_UP`, `SELESAI`, `LAIN`).
- Vertical timeline feed dengan auto-header tanggal kejadian, badge warna kategori, preview foto, dan link Google Maps.
- Auto-refresh polling AJAX timeline setiap 30 detik.
- Geolocation API untuk deteksi koordinat GPS teknis secara otomatis.

---

## [0.2.0] - 2026-09-21 - FASE 2: MODUL TIKET (OPEN / CLOSE)

### ✨ Added
- Generator otomatis Nomor Tiket: `BDG-YYYYMMDD-XXX`.
- `TiketService` & `TiketController` untuk pembuatan tiket baru, edit tiket berstatus OPEN, dan penutupan tiket (Closing).
- Form Open Tiket Baru dengan live preview nomor tiket via AJAX endpoint `/tiket/preview-number`.
- Filter canggih daftar tiket (pencarian teks, segment, status, status SLA, rentang tanggal).
- RoleMiddleware untuk proteksi aksi berdasarkan kewenangan role.

---

## [0.1.0] - 2026-09-21 - FASE 1: SETUP & AUTHENTICATION

### ✨ Added
- Inisialisasi arsitektur Laravel 12 dengan tema desain corporate navy/teal.
- Autentikasi multi-role: Admin, HelpDesk, Teknis, SA/CS, Client.
- Halaman Login interaktif dengan Network Canvas animation, toggler password, dan Quick Demo Login buttons.
- Skema migrasi database MySQL: `users`, `tiket`, `kronologis`, `resume_pekerjaan`, `material`, `titik_perbaikan`, `manuver_core`, `dokumentasi`, `master_sla`, `notifikasi_log`.
- Seeder akun default & SLA target awal.

---
*© 2026 PT Connecti Jelajah Priangan*
