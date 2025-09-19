# TenAspiration

**TenAspiration** adalah aplikasi web untuk mengumpulkan dan menampilkan aspirasi siswa di sekolah. Proyek ini terbagi menjadi dua bagian utama:

- **TA-backend** — Logika server dan API (menggunakan PHP & Blade template).
- **TA-frontend** — Antarmuka pengguna berbasis JavaScript.

---

## 📂 Struktur Proyek

```
TenAspiration/
├── TA-backend/        # Backend (PHP, API)
├── TA-frontend/       # Frontend (ReactJs)
├── .git/              # Data Git repository
├── README.md          # Dokumentasi proyek
└── ...                # File pendukung lainnya
```

---

## 🚀 Fitur Utama

- **Form Aspirasi** — Siswa dapat mengirim aspirasi melalui antarmuka web.
- **Daftar Aspirasi** — Menampilkan semua aspirasi yang sudah terkumpul.
- **Manajemen Backend** — Mendukung API untuk memproses data aspirasi.
- **Struktur Modular** — Backend dan frontend terpisah untuk kemudahan pengembangan.
- **Admin Dashboard** — Pengaturan data data aspirasi untuk admin.

---

## 🛠️ Teknologi yang Digunakan

### Backend
- **PHP** (Laravel)
- Database (MySQL)

### Frontend
- **Javascript** (ReactJs)
- Tailwindcss
- Integrasi dengan API backend

---

## 📦 Instalasi & Penggunaan

1. **Clone Repository**
   ```bash
   git clone https://github.com/LAWAGGG/TenAspiration.git
   cd TenAspiration
   ```

2. **Setup Backend**
   ```bash
   cd TA-backend
   composer install
   cp .env.example .env
   Note : ganti nama database terlebih dahulu di .env
   php artisan key:generate
   # Konfigurasi koneksi database di file .env
   php artisan dub:seed
   php artisan migrate
   php artisan serve
   ```

3. **Setup Frontend**
   ```bash
   cd ../TA-frontend
   npm install
   npm run dev
   ```

4. **Akses Aplikasi**
   - Backend API: `http://localhost:8000`
   - Frontend UI: `http://localhost:5173`

---

## 📸 Tampilan Web
### Form Aspirasi Harian
![Form Aspirasi Harian](https://res.cloudinary.com/dnm8qczle/image/upload/v1757747659/Screenshot_2025-09-13_140559_u49qso.png)

### Form Aspirasi Event
![Form Aspirasi Event](https://res.cloudinary.com/dnm8qczle/image/upload/v1757747659/Screenshot_2025-09-13_140544_dly8ir.png)


---

## 🤝 Kontribusi

1. Fork repository ini.
2. Buat branch fitur baru:  
   ```bash
   git checkout -b fitur-baru
   ```
3. Commit perubahan Anda:  
   ```bash
   git commit -m "Menambahkan fitur baru"
   ```
4. Push ke branch:  
   ```bash
   git push origin fitur-baru
   ```
5. Buat Pull Request.

---

## 👨‍💻 Pengembang
- **LAWAGGG** — [GitHub](https://github.com/LAWAGGG)

