# 🗳️ Flexible E-Voting System (Sistem E-Voting Terpadu)

[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-4.x-FB70A9?style=for-the-badge&logo=livewire)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-38B2AC?style=for-the-badge&logo=tailwind-css)](https://tailwindcss.com)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)

Sistem Pemilihan Elektronik (*E-Voting*) modern, aman, fleksibel, dan transparan yang dirancang dengan arsitektur enterprise menggunakan **Laravel 13**, **Livewire 4**, dan **Tailwind CSS v4**.

Sangat cocok untuk berbagai skala pemilihan:
- 🎓 **Kampus & Sekolah**: Pemilihan Ketua/Wakil BEM, DPM, Himpunan Mahasiswa (HIMA), Ketua OSIS/MPK.
- 🏢 **Organisasi & Komunitas**: Musyawarah Nasional (Munas), Ikatan Alumni, Yayasan, Koperasi.
- 🏛️ **Perusahaan & Lembaga**: Pemilihan Dewan Pengawas, Serikat Pekerja, Komite Internal.

---

## 🌟 Fitur Utama

### 1. 🛡️ Keamanan & Privasi Tingkat Tinggi
- **Zero-Knowledge Ballot Privacy**: Pilihan surat suara pemilih sama sekali tidak dihubungkan langsung ke ID pengguna di database (*decoupled anonymous ballot*), menjamin asas **LUBER** (Langsung, Umum, Bebas, Rahasia).
- **Double-Vote Protection**: Dilengkapi transaksi database dengan *Row-Level Locking* (`lockForUpdate`) untuk mencegah duplikasi suara secara konkruen.
- **Ballot Verification Token (`/verify/{token?}`)**: Setiap pemilih mendapatkan tanda terima berupa token unik terenkripsi (*cryptographic receipt token*) untuk memverifikasi secara mandiri bahwa suaranya telah sah terhitung di sistem tanpa membocorkan isi pilihannya.

### 2. 🏛️ Format Pemilihan Fleksibel (Single & Multi-Position)
- **Format Pasangan Calon (Paslon/Group)**: Pasangan Calon Ketua & Calon Wakil Ketua lengkap dengan foto, slogan, visi, dan misi terstruktur.
- **Format Jabatan Majemuk (Multi-Position)**: Pemilih dapat memilih kandidat untuk beberapa posisi jabatan sekaligus dalam satu sesi surat suara.
- **Visi & Misi Interaktif**: Calon pemilih dapat membaca visi, misi, dan profil lengkap kandidat melalui modal popup sebelum mencoblos.

### 3. 📊 Layar Monitor Kiosk Real-Time (`/screen`)
- **Hub Sesi Pemilihan**: Akses `/screen` untuk melihat seluruh katalog sesi pemilihan aktif.
- **Layar Monitor Panggung / TV Projector (`/screen/{slug}`)**: Tampilan layar penuh tanpa menu bar (*clean kiosk mode*) yang menampilkan grafik perolehan suara *real-time*, persentase partisipasi pemilih, jam digital 3 zona waktu, dan animasi podium pemimpin suara.

### 4. 🇮🇩 Dukungan 3 Zona Waktu Indonesia
- **WIB** – *Waktu Indonesia Barat* (`Asia/Jakarta`, UTC+7)
- **WITA** – *Waktu Indonesia Tengah* (`Asia/Makassar`, UTC+8)
- **WIT** – *Waktu Indonesia Timur* (`Asia/Jayapura`, UTC+9)
- Zona waktu dapat diatur secara dinamis di Pengaturan Website dan otomatis diterapkan ke seluruh tampilan waktu, jam digital monitor, bukti suara, dan berita acara.

### 5. 👥 Manajemen Pemilih (DPT) & Import Massal CSV
- **Import Massal DPT via CSV**: Unggah ribuan data pemilih (Nama, Email, NIM/NIK, Password kustom/default) dalam hitungan detik.
- **Unduh Template CSV**: Template berkas CSV bawaan sistem untuk memudahkan panitia.
- **Kontrol Hak Suara**: Fitur pencabutan/pengaktifan kembali hak suara dengan dialog konfirmasi SweetAlert2.

### 6. 🎨 Full Custom Branding & White-Label
- **Upload Logo Brand & Favicon**: Mendukung format PNG, JPG, SVG, WebP, dan ICO.
- **Kustomisasi Identitas**: Mengubah Nama Aplikasi, Nama Lembaga/Penyelenggara, Tagline, dan Teks Hak Cipta Footer langsung dari portal admin.

### 7. 📜 Berita Acara & Laporan Resmi Pemilu
- **Cetak Berita Acara Resmi (A4 Ready)**: Halaman rekapitulasi hasil penghitungan suara resmi (`/admin/elections/{id}/report`) lengkap dengan nomor surat, kop dinas, rincian suara sah, dan *SHA-256 Integrity Checksum*.
- **Audit Log Keamanan**: Jejak audit komprehensif mencatat setiap aktivitas penting sistem beserta User, IP Address, dan Metadata.

---

## 🏗️ Pola Arsitektur (Clean Architecture)

Aplikasi dibangun dengan prinsip **Clean Architecture & Domain-Driven Design (DDD) ringan**:

- **Thin Livewire Components**: Menerima request -> validasi -> otorisasi -> memanggil Action / Service -> response.
- **Single-Use Action Classes (`app/Actions`)**: Mengenkapsulasi alur transaksi bisnis (misal: `SubmitBallotAction`, `CreateElectionAction`, `UpdateWebsiteSettingsAction`, `ImportVotersAction`).
- **Domain Services (`app/Services`)**: Layanan reusable (misal: `VotingService`, `ResultService`, `AuditLogService`, `SettingService`, `ElectionReportService`).
- **Typed Readonly DTOs (`app/DTOs`)**: Transfer data yang aman dan bertipe antar-layer.
- **Query Objects (`app/Queries`)**: Query analitik dan agregasi data kompleks (misal: `GetElectionResults`, `GetLiveVotingStatistics`, `GetElectionVoters`).
- **UUID Primary Keys**: Semua entitas utama menggunakan UUID untuk keamanan maksimal.
- **Zero Inline Scripts/Styles**: Seluruh CSS/JS terpusat di `public/css` dan `public/js`.

---

## 🛠️ Tech Stack

- **Backend**: [PHP 8.3+](https://www.php.net/), [Laravel 13](https://laravel.com/)
- **Frontend & Reaktivitas**: [Livewire 4](https://livewire.laravel.com/), [Alpine.js](https://alpinejs.dev/)
- **Styling**: [Tailwind CSS v4](https://tailwindcss.com/)
- **UI Dialogs & Icons**: [SweetAlert2](https://sweetalert2.github.io/), [FontAwesome 6 Free](https://fontawesome.com/)
- **Database**: MySQL 8.0+ / SQLite (development) / PostgreSQL 15+

---

## 🚀 Panduan Instalasi & Menjalankan

### Persyaratan Sistem
- PHP `>= 8.3` (ekstensi: `pdo`, `sqlite3`/`pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`)
- Composer `>= 2.x`
- Node.js `>= 18.x` & NPM

### Langkah-langkah Instalasi:

1. **Clone Repositori**:
   ```bash
   git clone git@github.com:willyrahmaw/e-vote.git
   cd e-vote
   ```

2. **Install Dependensi PHP & JavaScript**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Buat Storage Symlink**:
   ```bash
   php artisan storage:link
   ```

5. **Migrasi Database & Seeder**:
   ```bash
   touch database/database.sqlite  # jika menggunakan SQLite
   php artisan migrate --seed
   ```

6. **Jalankan Server Development**:
   ```bash
   # Jalankan server aplikasi
   php artisan serve
   ```
   Buka browser di: `http://localhost:8000`

---

## 🔑 Akun Demo (Default Credentials)

Data seeder menyediakan akun berikut untuk pengujian:

| Role | Email / Akun | Password | Identitas / NIM | Akses & Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| **Administrator** | `admin@example.com` | `password` | `ADM-001` | Akses penuh dashboard admin, wizard pemilihan, DPT, laporan & pengaturan |
| **Pemilih (Voter)** | `voter@example.com` | `password` | `MHS-2026001` | Akun pemilih aktif (belum memilih, siap uji coba bilik suara) |
| **Pemilih 2 - 25** | `voter2@example.com` s/d `voter25@example.com` | `password` | `MHS-2026002` dst. | Akun simulasi partisipasi pemilih massal |

---

## 🗺️ Peta Rute Aplikasi

### 🔓 Akses Publik & Kiosk
- `/login` : Autentikasi masuk pengguna (mendukung Email atau NIM/NIK)
- `/screen` : Hub katalog layar monitor pemilihan
- `/screen/{election}` : **Layar Besar / TV Projector Kiosk** penghitungan suara realtime
- `/verify/{token?}` : **Verifikasi Surat Suara Independen** (Zero-Knowledge Verifier)

### 🛡️ Portal Administrator (`/admin/*`)
- `/admin/dashboard` : Ringkasan statistik pemilihan, DPT, dan tingkat partisipasi
- `/admin/elections` : Manajemen daftar pemilihan, aktivasi, penutupan, & hapus sesi
- `/admin/elections/wizard` : Wizard pembuatan pemilihan step-by-step
- `/admin/elections/{id}/report` : Berita Acara & Rekapitulasi Hasil Resmi (A4 Printable)
- `/admin/candidates` : Master data kandidat dan pasangan calon
- `/admin/voters` : Manajemen DPT & Import Massal CSV
- `/admin/live-voting` : Monitoring grafik dan quick count langsung
- `/admin/audit-logs` : Log aktivitas dan audit keamanan sistem
- `/admin/settings` : Pengaturan logo, favicon, zona waktu, identitas lembaga, & fitur

### 🗳️ Portal Pemilih (`/portal/*`)
- `/portal/dashboard` : Daftar pemilihan aktif & riwayat partisipasi DPT
- `/portal/profile` : Kelola profil pemilih & ubah password
- `/portal/elections/{election}/vote` : Bilik suara digital interaktif (*Ballot Box*)
- `/portal/elections/{election}/success` : Bukti tanda terima suara beserta **Verification Token**
- `/portal/elections/{election}/results` : Halaman hasil quick count (jika diizinkan)

---

## 🧪 Pengujian Otomatis (Automated Testing)

Aplikasi dilengkapi unit test dan feature test komprehensif untuk memastikan validasi surat suara, integritas data, proteksi double-vote, dan keamanan otorisasi:

```bash
php artisan test
```

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).
