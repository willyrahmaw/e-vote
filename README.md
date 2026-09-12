# 🗳️ Flexible E-Voting System (Sistem E-Voting Modern)

Sistem Pemilihan Elektronik (*E-Voting*) modern, aman, fleksibel, dan transparan yang dibangun dengan arsitektur enterprise menggunakan **Laravel 13**, **Livewire 4**, dan **Tailwind CSS v4**.

Dirancang untuk berbagai skala pemilihan (Organisasi Mahasiswa / BEM / DPM, Organisasi Kemasyarakatan, Yayasan, hingga Perusahaan) dengan integritas data tinggi dan perlindungan privasi pemilih yang ketat.

---

## 🌟 Fitur Utama

### 1. 🛡️ Keamanan & Privasi Tingkat Tinggi
- **Zero-Knowledge Ballot Privacy**: Pilihan pemilih pada surat suara sama sekali tidak dihubungkan langsung ke ID pengguna di database, menjamin kerahasiaan pilihan (LUBER - Langsung, Umum, Bebas, Rahasia).
- **Anti-Double Voting**: Dilengkapi proteksi transaksi database dengan *Row-Level Locking* (`lockForUpdate`) untuk mencegah duplikasi suara secara konkruen.
- **Ballot Verification Token**: Setiap pemilih mendapatkan token unik terenkripsi/hash untuk memverifikasi secara mandiri bahwa surat suaranya telah sah terhitung di sistem tanpa membocorkan pilihannya.

### 2. 🏛️ Format Pemilihan yang Sangat Fleksibel
- **Pasangan Calon (Group/Paslon)**: Cocok untuk pemilihan Presiden/Wakil Presiden Mahasiswa, Ketua/Wakil Ketua Umum.
- **Multi-Posisi (Jabatan Majemuk)**: Pemilih dapat memilih beberapa kandidat untuk berbagai posisi jabatan sekaligus dalam satu sesi surat suara (contoh: Ketua Fraksi, Sekretaris Jenderal, Anggota Dewan).
- **Kandidat Tunggal / Opsi Abstain / Kotak Kosong**: Konfigurasi suara abstain dan batas minimal/maksimal pilihan pada setiap jabatan.

### 3. 📊 Hasil Real-Time & Layar Monitor TV (Kiosk Mode)
- **Live Voting Dashboard**: Grafik perolehan suara *real-time* berbasis persentase, total suara masuk, dan tingkat partisipasi pemilih.
- **Standalone Big Screen Kiosk (`/screen/{election}`)**: Tampilan layar penuh tanpa menu bar, dioptimalkan untuk monitor/proyektor di panggung penghitungan suara terbuka.

### 4. 🔍 Transparansi & Audit Trail
- **Audit Log Lengkap**: Mencatat setiap aktivitas penting sistem (pembuatan pemilihan, perubahan status, login, rekapitulasi) beserta User ID, IP Address, dan Metadata.
- **Laporan & Rekapitulasi Pemilu**: Halaman rekapitulasi komprehensif untuk berita acara pemilihan dan ekspor data hasil akhir.

### 5. 👥 Manajemen Pemilih & Kandidat
- **Data Pemilih Tetap (DPT)**: Manajemen voter per pemilihan dengan status kelayakan (*eligibility*) dan status partisipasi (*has voted*).
- **Election Wizard**: Alur interaktif step-by-step untuk membuat pemilihan baru, konfigurasi tanggal & jadwal, posisi, dan paslon/kandidat.

---

## 🏗️ Pola Arsitektur

Aplikasi ini mengadopsi prinsip **Clean Architecture & Domain-Driven Design (DDD) ringan**:

- **Thin Livewire Components & Controllers**: Komponen UI hanya bertugas menangani *request validation*, otorisasi, dan memanggil aksi/layanan domain.
- **Single-Use Action Classes (`app/Actions`)**: Mengenkapsulasi alur bisnis utama (misal: `SubmitBallotAction`, `CreateElectionAction`, `ActivateElectionAction`).
- **Domain Services (`app/Services`)**: Logika bisnis reusable (misal: `VotingService`, `ResultService`, `AuditLogService`, `CandidateService`).
- **Typed Readonly DTOs (`app/DTOs`)**: Transfer data yang aman dan bertipe antar-layer (misal: `SubmitBallotData`, `CreateElectionData`).
- **Query Objects (`app/Queries`)**: Agregasi data kompleks dan analitik (misal: `GetElectionResults`, `GetLiveVotingStatistics`).
- **UUID Primary Keys**: Semua entitas utama menggunakan UUID untuk mencegah *enumeration attack*.

---

## 🛠️ Tech Stack

- **Backend**: [PHP 8.3+](https://www.php.net/), [Laravel 13](https://laravel.com/)
- **Frontend / Reactivity**: [Livewire 4](https://livewire.laravel.com/), Alpine.js
- **Styling**: [Tailwind CSS v4](https://tailwindcss.com/), Vite
- **Database**: SQLite (default untuk development) / MySQL 8.0+ / PostgreSQL 15+

---

## 🚀 Panduan Instalasi & Menjalankan

### Persyaratan Sistem
- PHP `>= 8.3` (dengan ekstensi: `pdo`, `sqlite3`, `mbstring`, `openssl`, `curl`)
- Composer `>= 2.x`
- Node.js `>= 18.x` & NPM

### Langkah-langkah:

1. **Clone Repositori & Masuk ke Direktori Proyek**:
   ```bash
   git clone <repository-url>
   cd e-vote
   ```

2. **Install Dependensi PHP & JavaScript**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**:
   Salin file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrasi Database & Seeder**:
   Jalankan migrasi tabel dan seed data awal:
   ```bash
   touch database/database.sqlite  # jika menggunakan SQLite
   php artisan migrate --seed
   ```

5. **Jalankan Server Development**:
   Jalankan server aplikasi dan Vite asset bundler:
   ```bash
   # Opsi 1: Jalankan secara bersamaan menggunakan Composer Dev Script
   composer run dev

   # Opsi 2: Jalankan terpisah di dua terminal
   # Terminal 1:
   php artisan serve
   # Terminal 2:
   npm run dev
   ```

6. Akses aplikasi melalui browser di: `http://localhost:8000`

---

## 🔑 Akun Demo (Default Credentials)

Data seeder menyediakan akun berikut untuk pengujian:

| Role | Email | Password | Identitas / NIM | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| **Administrator** | `admin@example.com` | `password` | `ADM-001` | Akses penuh portal admin, audit log, manajemen kandidat & wizard pemilihan |
| **Pemilih (Voter)** | `voter@example.com` | `password` | `MHS-2026001` | Akun pemilih aktif (belum memilih, siap uji coba voting) |
| **Voters Tambahan** | `voter2@example.com` s.d `voter25@example.com` | `password` | `MHS-2026002` dst. | Akun simulasi partisipasi pemilih |

---

## 🗺️ Peta Navigasi & Rute Utama

### 🔓 Akses Publik & Kiosk
- `/login` : Halaman autentikasi login terpadu
- `/screen/{election}` atau `/live/{election}` : **Layar Besar / TV Monitor Kiosk** hasil perolehan suara *real-time*
- `/verify/{token?}` : **Halaman Verifikasi Surat Suara Terbuka** (Zero-Knowledge Verifier)
- `/live-results/{election}` : Halaman hasil publik

### 🛡️ Portal Administrator (`/admin/*`)
- `/admin/dashboard` : Ringkasan statistik pemilihan, DPT, dan tingkat partisipasi
- `/admin/elections` : Manajemen daftar pemilihan, aktivasi, dan penutupan suara
- `/admin/elections/wizard` : Wizard pembuatan pemilihan baru
- `/admin/candidates` : Manajemen master data kandidat
- `/admin/voters` : Manajemen data pemilih tetap (DPT) & generator akun pemilih
- `/admin/live-voting` : Monitoring grafik dan quick count langsung
- `/admin/audit-logs` : Catatan log aktivitas dan jejak audit keamanan
- `/admin/settings` : Pengaturan profil organisasi & sistem

### 🗳️ Portal Pemilih (`/portal/*`)
- `/portal/dashboard` : Daftar pemilihan aktif dan riwayat partisipasi
- `/portal/elections/{election}/vote` : Bilik suara digital (*Ballot Box*) interaktif
- `/portal/elections/{election}/success` : Bukti tanda terima suara beserta **Verification Token**
- `/portal/elections/{election}/results` : Halaman hasil perolehan suara setelah memilih

---

## 🧪 Menjalankan Pengujian (Testing)

Untuk memastikan seluruh alur bisnis, proteksi anti-double vote, dan kalkulasi suara berjalan sesuai standar:

```bash
php artisan test
```

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).
