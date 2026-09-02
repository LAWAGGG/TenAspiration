<div align="center">
  <h1>TenAspiration</h1>
  <p>Sistem aspirasi, umpan balik acara, dan pengaduan rahasia berbasis web untuk lingkungan sekolah.</p>
</div>

## Tentang

**TenAspiration** adalah aplikasi web yang dikembangkan untuk memfasilitasi penyaluran aspirasi, kritik, saran, dan pengaduan secara anonim di lingkungan sekolah. Aplikasi ini menyediakan tiga jenis layanan pengaduan/aspirasi yang dapat diakses oleh siswa melalui form publik.

Dibangun dengan **Laravel 12** di sisi backend dan **Blade** + **Tailwind CSS** + **Alpine.js** di sisi frontend.

## Fitur

### Tiga Jenis Aspirasi

1. **Audiensi / Voxes** — Aspirasi umum yang ditujukan ke bidang-bidang tertentu di sekolah (kesiswaan, sarpras, kurikulum, humas, tata usaha, OSIS, MPK, ekskul, dan umum). Siswa memilih kelas (X/XI/XII) dan menulis pesan yang akan diteruskan ke bidang terkait.

2. **Aspirasi Acara** — Umpan balik spesifik terhadap suatu acara sekolah. Siswa dapat memilih acara yang sedang berlangsung lalu memberikan pesan, kesan, perubahan yang diharapkan, dan momen buruk selama acara.

3. **Keluh Kesah** — Pengaduan rahasia yang bersifat pribadi. Siswa dapat menyertakan nomor telepon untuk dihubungi balik oleh pihak terkait.

### Panel Admin

Setelah login, admin dapat:

- **Dashboard** — Melihat statistik ringkas: total aspirasi, aspirasi hari ini, jumlah keluh kesah, dan daftar acara. Dilengkapi grafik interaktif (Chart.js) untuk tren 30 hari, sebaran per bidang, dan per kelas.
- **Kelola Aspirasi** — Melihat, mencari, menyaring, mengedit, menghapus, dan menghapus massal data aspirasi.
- **Kelola Keluh Kesah** — Melihat detail pengaduan rahasia (termasuk nomor telepon), menghapus, dan ekspor CSV.
- **Kelola Acara** — Membuat, mengedit, menghapus, dan mengatur visibilitas acara (acara yang disembunyikan tidak muncul di form publik).
- **Kustomisasi Form** — Mengubah pertanyaan yang tampil di setiap form publik (Audiensi, Acara, Keluh Kesah).
- **Manajemen Email Target** — Mengelola daftar alamat email yang akan menerima notifikasi saat pengaduan baru masuk.
- **Tautan Publik** — Membuat tautan yang dapat dibagikan ke publik untuk menampilkan data aspirasi yang sudah difilter.
- **Ekspor CSV** — Mengekspor data aspirasi ke file CSV dengan pembatas titik koma.

### Filter Kata Kotor

Semua input teks pada form publik melewati filter kata kotor bahasa Indonesia. Jika terdeteksi, pengiriman akan ditolak.

## Tech Stack

| Lapisan | Teknologi |
|---------|-----------|
| **Backend** | PHP ^8.2, Laravel ^12.0 |
| **Frontend** | Blade Templates, Tailwind CSS v3, Alpine.js v3 |
| **Database** | SQLite (default), dapat diganti ke MySQL |
| **Build Tool** | Vite 7 |
| **Chart** | Chart.js 4.4.7 |
| **Testing** | Pest PHP ^4.1 |

## Persyaratan Sistem

- PHP ^8.2
- Composer
- Node.js & npm
- SQLite (atau MySQL)

## Instalasi & Menjalankan

1. Clone repositori dan masuk ke direktori project:
   ```bash
   git clone <url-repo> tenaspiration
   cd tenaspiration
   ```

2. Install dependencies PHP:
   ```bash
   composer install
   ```

3. Install dependencies frontend:
   ```bash
   npm install && npm run build
   ```

4. Salin file environment dan sesuaikan:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Edit `.env` sesuai kebutuhan (database, mail, dll). Secara default aplikasi menggunakan SQLite.

5. Jalankan migrasi database:
   ```bash
   php artisan migrate
   ```

6. Jalankan aplikasi:
   ```bash
   php artisan serve
   ```

7. Akses aplikasi di `http://localhost:8000`.

### Catatan Notifikasi Email

Untuk mengirim notifikasi email saat pengaduan rahasia masuk, konfigurasikan pengaturan SMTP di `.env`. Secara default, email akan ditulis ke `storage/logs/laravel.log` (driver `log`). Jika ingin mengirim email sungguhan:

- Set `MAIL_MAILER=smtp`
- Isi `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`, `MAIL_FROM_ADDRESS` sesuai penyedia email
- Jalankan queue worker: `php artisan queue:work`

## Struktur Database

Tabel utama:

| Tabel | Deskripsi |
|-------|-----------|
| `users` | Pengguna admin/wakil |
| `aspirations` | Aspirasi umum (Audiensi/Voxes) |
| `aspiration_events` | Aspirasi spesifik berdasarkan acara |
| `aspiration_keluh_kesah` | Pengaduan rahasia |
| `events` | Acara sekolah yang dapat menerima umpan balik |
| `form_questions` | Definisi pertanyaan dinamis untuk setiap form |
| `shared_links` | Tautan publik yang berisi data aspirasi terfilter |
| `target_emails` | Alamat email penerima notifikasi pengaduan |

## Hak Akses

- **Admin** — Akses penuh ke dashboard, semua jenis aspirasi, dan manajemen data.
- **Wakil** — Akses terbatas, hanya dapat melihat data keluh kesah.

## Lisensi

Hak cipta milik pengembang. Tidak untuk didistribusikan tanpa izin.
