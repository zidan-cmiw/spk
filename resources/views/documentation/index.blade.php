@extends('layouts.app')

@section('title', 'Dokumentasi Sistem & Model SPK')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="card border-0 mb-4 bg-white shadow-sm">
        <div class="card-body p-4">
            <span class="badge bg-primary-subtle text-primary mb-2 px-3 py-1 rounded-pill">
                <i class="bi bi-book-half me-1"></i> Dokumentasi Teknis & Rancangan Sistem
            </span>
            <h3 class="fw-bold mb-1">Rancangan SPK Penilaian Prestasi Siswa: Integrasi AHP + SMART</h3>
            <p class="text-muted mb-0">
                Dokumen komprehensif 4 pilar pengembangan: Fiksasi Model, Desain Sistem, Persiapan Database, dan Environment Pemrograman berdasarkan jurnal acuan Herdiana dkk. (Juni 2024).
            </p>
        </div>
    </div>

    <!-- 4 Section Cards -->
    <div class="row g-4">
        <!-- 1. Fiksasi Model -->
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 text-primary">
                        <i class="bi bi-1-circle-fill me-2"></i>1. Fiksasi Model: Integrasi AHP dan SMART
                    </h5>
                    <span class="badge bg-success-subtle text-success border border-success-subtle">Model Validated</span>
                </div>
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark">1.1 Latar Belakang & Alasan Integrasi</h6>
                    <p class="text-muted" style="text-align: justify;">
                        Pada jurnal acuan (<em>Herdiana dkk., 2024</em>), metode SMART digunakan dengan bobot yang ditetapkan secara manual/subjektif (K1=40%, K2=20%, K3=15%, K4=15%, K5=10%) tanpa dasar perhitungan matematis dan tanpa uji konsistensi. Hal ini berisiko menghasilkan keputusan yang bias pengambil keputusan. 
                        Untuk mengatasi kelemahan tersebut, sistem ini mengintegrasikan <strong>Analytic Hierarchy Process (AHP)</strong> untuk menentukan bobot kriteria secara objektif melalui matriks perbandingan berpasangan (pairwise comparison) skala Saaty 1–9 dan uji konsistensi rasio ($CR < 0,1$), sedangkan metode <strong>SMART</strong> tetap dipertahankan untuk perangkingan alternatif yang cepat dan adaptif.
                    </p>

                    <div class="table-responsive my-3">
                        <table class="table table-bordered table-sm text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Aspek Penilaian</th>
                                    <th>Jurnal Acuan (Herdiana dkk., 2024)</th>
                                    <th>Sistem Rancang Bangun Ini</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold text-start">Penentuan Bobot</td>
                                    <td>Manual / Subjektif (40/20/15/15/10) tanpa uji ilmiah</td>
                                    <td class="text-success fw-bold">AHP: Matriks berpasangan Saaty + Uji Konsistensi ($CR < 0,1$)</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-start">Perangkingan Alternatif</td>
                                    <td>SMART (Simple Multi Attribute Rating Technique)</td>
                                    <td class="fw-bold text-primary">SMART (Menggunakan Bobot Ternormalisasi AHP)</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-start">Validitas Keputusan</td>
                                    <td>Rentan inkonsistensi pertimbangan pengambil keputusan</td>
                                    <td class="text-success fw-bold">Teruji secara matematis dan transparan</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h6 class="fw-bold text-dark mt-4">1.2 Rumus Matematis & Perbaikan Rumus Jurnal</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <strong class="text-primary d-block mb-1">Tahap AHP (Pembobotan & CR):</strong>
                                <ol class="small ps-3 mb-0">
                                    <li>Matriks perbandingan berpasangan $A_{n \times n}$ dengan skala Saaty 1–9, di mana $a_{ji} = 1 / a_{ij}$.</li>
                                    <li>Normalisasi kolom: $r_{ij} = a_{ij} / \sum_{k=1}^n a_{kj}$.</li>
                                    <li>Bobot prioritas kriteria: $w_i = \frac{1}{n} \sum_{j=1}^n r_{ij}$.</li>
                                    <li>Uji Konsistensi: $\lambda_{max} = \frac{1}{n} \sum \frac{(A \cdot w)_i}{w_i}$, $CI = \frac{\lambda_{max} - n}{n - 1}$, $CR = \frac{CI}{RI}$.</li>
                                    <li>Syarat konsisten: <strong>$CR < 0,1$</strong> ($RI=1,12$ untuk $n=5$).</li>
                                </ol>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <strong class="text-info d-block mb-1">Tahap SMART (Utility & Nilai Akhir):</strong>
                                <ol class="small ps-3 mb-0">
                                    <li>Normalisasi bobot: Mengadopsi bobot hasil AHP ($\sum w_j = 1$).</li>
                                    <li>Skor parameter $C_{out}$ dikonversi dari rentang nilai (Tabel 3 Jurnal).</li>
                                    <li>
                                        <strong>Koreksi Rumus Utility:</strong> Pada Persamaan (2) jurnal tertulis rumus cost, namun perhitungannya pada Tabel 5 memakai rumus <em>Benefit</em>:
                                        <div class="bg-white p-1 my-1 border text-center font-monospace">
                                            $u_j(a_i) = \frac{C_{out} - C_{min}}{C_{max} - C_{min}} \times 100$
                                        </div>
                                        dengan $C_{min}=20$ dan $C_{max}=100$.
                                    </li>
                                    <li>Nilai akhir: $u(a_i) = \sum_{j=1}^m w_j \cdot u_j(a_i)$.</li>
                                    <li>Perankingan: Nilai terbesar menunjukkan siswa terbaik.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Desain Sistem & Arsitektur -->
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 text-primary">
                        <i class="bi bi-2-circle-fill me-2"></i>2. Desain Sistem & Arsitektur Aplikasi
                    </h5>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">MVC Berlapis</span>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted">
                        Aplikasi dibangun menggunakan pola arsitektur <strong>Model-View-Controller (MVC) berlapis</strong> dengan pemisahan logika perhitungan bisnis pada <strong>Service Layer</strong> (<code>AHPService</code> dan <code>SMARTService</code>). Pemisahan ini menjaga kode tetap bersih, mudah diuji (unit testing), dan mudah dipelihara.
                    </p>

                    <div class="row g-3 text-center my-2">
                        <div class="col-md">
                            <div class="p-3 border rounded-3 bg-white">
                                <div class="badge bg-secondary mb-1">Pengguna</div>
                                <h6 class="fw-bold mb-0">Admin / Guru / Kepsek</h6>
                                <small class="text-muted">Hak akses & peran</small>
                            </div>
                        </div>
                        <div class="col-md-auto d-flex align-items-center justify-content-center text-muted fs-4">
                            &rarr;
                        </div>
                        <div class="col-md">
                            <div class="p-3 border rounded-3 bg-white">
                                <div class="badge bg-primary mb-1">View</div>
                                <h6 class="fw-bold mb-0">Blade + Bootstrap + Chart.js</h6>
                                <small class="text-muted">Antarmuka Responsif</small>
                            </div>
                        </div>
                        <div class="col-md-auto d-flex align-items-center justify-content-center text-muted fs-4">
                            &rarr;
                        </div>
                        <div class="col-md">
                            <div class="p-3 border rounded-3 bg-white">
                                <div class="badge bg-info mb-1">Controller</div>
                                <h6 class="fw-bold mb-0">HTTP Controllers</h6>
                                <small class="text-muted">Request Handler</small>
                            </div>
                        </div>
                        <div class="col-md-auto d-flex align-items-center justify-content-center text-muted fs-4">
                            &rarr;
                        </div>
                        <div class="col-md">
                            <div class="p-3 border rounded-3 bg-white">
                                <div class="badge bg-success mb-1">Service Layer</div>
                                <h6 class="fw-bold mb-0">AHPService & SMARTService</h6>
                                <small class="text-muted">Matematika & Perhitungan</small>
                            </div>
                        </div>
                        <div class="col-md-auto d-flex align-items-center justify-content-center text-muted fs-4">
                            &rarr;
                        </div>
                        <div class="col-md">
                            <div class="p-3 border rounded-3 bg-white">
                                <div class="badge bg-dark mb-1">Database</div>
                                <h6 class="fw-bold mb-0">MySQL (Eloquent ORM)</h6>
                                <small class="text-muted">Penyimpanan Terstruktur</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Persiapan Database -->
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 text-primary">
                        <i class="bi bi-3-circle-fill me-2"></i>3. Persiapan Database (MySQL `spk_ahp_smart`)
                    </h5>
                    <span class="badge bg-dark-subtle text-dark border">9 Entitas Terelasi</span>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted">
                        Database dirancang dengan skema relasional 3NF terstruktur untuk mencakup seluruh kebutuhan data master kriteria, parameter, alternatif siswa, sesi matriks AHP, dan hasil perangkingan SMART:
                    </p>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle text-start mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 180px;">Nama Tabel</th>
                                    <th>Kolom Utama</th>
                                    <th>Fungsi dalam SPK</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>periods</code></td>
                                    <td>id, nama, semester, is_active</td>
                                    <td>Menyimpan tahun ajaran dan semester aktif penilaian.</td>
                                </tr>
                                <tr>
                                    <td><code>criteria</code></td>
                                    <td>id, kode (K1-K5), nama, tipe, bobot_default, urutan</td>
                                    <td>Menyimpan 5 kriteria baku prestasi siswa.</td>
                                </tr>
                                <tr>
                                    <td><code>parameters</code></td>
                                    <td>id, criteria_id, label, batas_min, batas_max, skor</td>
                                    <td>Sub-kriteria konversi nilai rapor ke skor parameter 20–100 (Tabel 3 Jurnal).</td>
                                </tr>
                                <tr>
                                    <td><code>students</code></td>
                                    <td>id, kode (A1-A5..), nis, nama, kelas, jurusan, jenis_kelamin</td>
                                    <td>Master data siswa kandidat / alternatif penilaian.</td>
                                </tr>
                                <tr>
                                    <td><code>ahp_sessions</code></td>
                                    <td>id, period_id, nama_sesi, lambda_max, ci, cr, is_consistent</td>
                                    <td>Riwayat sesi pengujian matriks perbandingan AHP dan status CR.</td>
                                </tr>
                                <tr>
                                    <td><code>ahp_comparisons</code></td>
                                    <td>id, session_id, criteria_a_id, criteria_b_id, nilai</td>
                                    <td>Menyimpan nilai matriks perbandingan berpasangan skala Saaty (1–9).</td>
                                </tr>
                                <tr>
                                    <td><code>criteria_weights</code></td>
                                    <td>id, session_id, criteria_id, bobot</td>
                                    <td>Menyimpan vektor bobot prioritas hasil perhitungan AHP.</td>
                                </tr>
                                <tr>
                                    <td><code>assessments</code></td>
                                    <td>id, student_id, criteria_id, period_id, nilai_asli, skor_parameter</td>
                                    <td>Menyimpan nilai parameter hasil penilaian tiap siswa ($C_{out}$).</td>
                                </tr>
                                <tr>
                                    <td><code>calculation_results</code></td>
                                    <td>id, period_id, session_id, student_id, mode_bobot, utility_scores, nilai_akhir, ranking</td>
                                    <td>Menyimpan rekapitulasi nilai akhir dan peringkat rekomendasi siswa.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Persiapan Environment Pemrograman -->
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 text-primary">
                        <i class="bi bi-4-circle-fill me-2"></i>4. Persiapan Environment Pemrograman
                    </h5>
                    <span class="badge bg-info-subtle text-info border border-info-subtle">Laravel 13 &bull; PHP 8.5 &bull; Laragon</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-dark">Spesifikasi Teknologi yang Digunakan:</h6>
                            <ul class="small ps-3 text-muted">
                                <li><strong>Environment Server:</strong> Laragon Portable Stack (Apache 2.4, MySQL 5.7 / MariaDB).</li>
                                <li><strong>Bahasa Pemrograman:</strong> PHP 8.5+ CLI & Web Module (Modern & High-Performance).</li>
                                <li><strong>Framework Backend:</strong> <strong>Laravel 13.x</strong> (Pembaruan signifikan dari CodeIgniter 3 yang sudah usang pada jurnal asli).</li>
                                <li><strong>Database Engine:</strong> MySQL <code>spk_ahp_smart</code> dengan koneksi PDO.</li>
                                <li><strong>Front-End UI:</strong> Bootstrap 5.3, Bootstrap Icons, Google Fonts Plus Jakarta Sans.</li>
                                <li><strong>Visualisasi Grafik:</strong> Chart.js 4 (Bar Chart dan Responsive Canvas).</li>
                            </ul>
                        </div>

                        <div class="col-md-6">
                            <h6 class="fw-bold text-dark">Langkah Eksekusi & Menjalankan Aplikasi:</h6>
                            <div class="bg-dark text-light p-3 rounded-3 font-monospace small">
                                # 1. Masuk direktori proyek<br>
                                cd c:\laragon\www\spk<br><br>
                                # 2. Migrasi database dan seeder jurnal<br>
                                php artisan migrate:fresh --seed<br><br>
                                # 3. Akses via Browser:<br>
                                http://localhost/spk/ atau http://spk.test/
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
