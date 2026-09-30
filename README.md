# SPK Penilaian Prestasi Siswa: Integrasi AHP + SMART

Sistem Pendukung Keputusan (SPK) untuk penilaian dan rekomendasi siswa berprestasi pada jenjang SMK/SMA, yang mengintegrasikan metode **Analytic Hierarchy Process (AHP)** untuk penentuan bobot kriteria berpasangan dengan uji konsistensi ($CR < 0,1$), serta metode **Simple Multi Attribute Rating Technique (SMART)** untuk perangkingan alternatif siswa.

Aplikasi ini dikembangkan berdasarkan studi kasus pada:
> **Jurnal Acuan:**  
> Rudi Bambang Herdiana, Yus Jayusman, Khoirida Aelani, Linda Hamidah (Juni 2024).  
> *"Implementasi Metode SMART dalam Sistem Pendukung Keputusan Penilaian Prestasi Belajar Siswa pada SMK Mandiri"*.  
> Jurnal Teknologi Informasi dan Komunikasi, Vol. 13 No. 1.

---

## 🚀 Fitur Utama

1. **Dashboard Eksekutif**:
   - Ringkasan data master (kriteria, alternatif siswa, sesi AHP, periode aktif).
   - Visualisasi bagan alur model terintegrasi AHP + SMART.
   - Preview Top 3 Siswa Berprestasi Utama.

2. **Master Kriteria & Parameter (Tabel 1 & 3 Jurnal)**:
   - 5 Kriteria baku: Pengetahuan (K1), Keterampilan (K2), Sikap (K3), Kehadiran/Absensi (K4), Ekstrakurikuler (K5).
   - Seluruh kriteria bertipe *Benefit*.
   - Tabel parameter rentang nilai rapor ke skor baku skala 20 – 100.

3. **Data Alternatif Siswa (Input Alternatif)**:
   - Manajemen data siswa kandidat seleksi.
   - Disertai 5 data alternatif acuan dari jurnal (A1 Ahmad Fauzi s.d A5 Eka Rahmawati).
   - Form input alternatif siswa baru.

4. **Input Penilaian Alternatif ($C_{out}$ / Tabel 4 Jurnal)**:
   - Matriks input skor parameter untuk seluruh alternatif.
   - Batch inline editor & modal input per siswa.
   - Tombol reset ke data sampel asli jurnal (A1 - A5).

5. **Modul AHP (Analytic Hierarchy Process)**:
   - Kuesioner perbandingan berpasangan (*pairwise comparison*) skala Saaty 1–9.
   - Perhitungan matriks perbandingan ($A$), matriks ternormalisasi ($R$), dan bobot prioritas ($w_j$).
   - Kalkulasi $\lambda_{max}$, Consistency Index (CI), dan Consistency Ratio (CR).
   - Verifikasi konsistensi ($CR < 0,1$) dengan Random Index ($RI = 1,12$ untuk $n=5$).
   - Fitur one-click *Preset Konsisten Jurnal* ($CR = 0,0074$).

6. **Modul Perangkingan SMART**:
   - Kalkulasi nilai utilitas benefit: $u_j = \frac{C_{out} - 20}{100 - 20} \times 100$.
   - Perkalian bobot ternormalisasi: $u(a_i) = \sum w_j \cdot u_j$.
   - **Mode Toggle**:
     - *Bobot AHP (Terintegrasi)*: Bobot matematis hasil AHP berpasangan ($CR < 0,1$).
     - *Bobot Manual Jurnal*: Replikasi persis bobot manual 40/20/15/15/10.
   - **Analisis Sensitivitas**: Tabel komparasi pergeseran ranking bobot manual vs bobot AHP.
   - Visualisasi grafik interaktif (Chart.js Bar Chart).

7. **Laporan Cetak Resmi**:
   - Surat Keputusan penetapan siswa berprestasi SMK Mandiri siap cetak / simpan PDF.
   - Lengkap dengan kop surat, nomor surat, tabel perankingan, dan kolom tanda tangan kepala sekolah.

8. **Dokumentasi In-App**:
   - Penjelasan teknis 4 pilar perancangan SPK langsung di dalam web.

---

## 🛠️ Spesifikasi Teknologi

- **Backend Framework**: [Laravel 13.x](https://laravel.com/)
- **Bahasa Pemrograman**: PHP 8.5+
- **Database Engine**: MySQL 5.7 / 8.0 / MariaDB
- **Frontend UI**: Bootstrap 5.3, Bootstrap Icons, Google Fonts Plus Jakarta Sans
- **Grafik & Visualisasi**: Chart.js 4.x
- **Testing**: PHPUnit / Pest (100% Passed)

---

## 📦 Panduan Instalasi Lokal

### 1. Prasyarat
- PHP >= 8.2 (disarankan PHP 8.2, 8.3, 8.4, atau 8.5)
- Composer 2.x
- MySQL Server (misal via Laragon atau XAMPP)

### 2. Kloning Repository
```bash
git clone https://github.com/zidan-cmiw/spk.git
cd spk
```

### 3. Instalasi Dependency
```bash
composer install
```

### 4. Konfigurasi Environment (`.env`)
Salin berkas `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Sesuaikan konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spk_ahp_smart
DB_USERNAME=root
DB_PASSWORD=root
```
Buat database `spk_ahp_smart` pada MySQL Anda jika belum ada.

### 5. Generate Application Key
```bash
php artisan key:generate
```

### 6. Migrasi & Seeder Database
Jalankan migrasi dan seeder untuk memuat data awal dari jurnal:
```bash
php artisan migrate --seed
```

### 7. Jalankan Server
Gunakan Laragon (langsung diakses via `http://localhost/spk/` atau virtual host `http://spk.test/`), atau gunakan built-in server:
```bash
php artisan serve
```
Akses di browser: `http://localhost:8000`

---

## 🧪 Menjalankan Automated Tests

Aplikasi dilengkapi unit test untuk memverifikasi logika matematika AHP dan SMART:
```bash
php artisan test
```

---

## 📄 Lisensi
Open-source di bawah lisensi [MIT](LICENSE).
