@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-0">
    <!-- Hero Welcome Banner -->
    <div class="card border-0 text-white mb-4 shadow-sm" style="background: linear-gradient(135deg, #3730a3 0%, #4f46e5 50%, #0284c7 100%); border-radius: 16px;">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-white text-primary fw-bold mb-2 px-3 py-1 rounded-pill">
                        <i class="bi bi-mortarboard-fill me-1"></i> SPK Evaluasi Prestasi Belajar SMK Mandiri
                    </span>
                    <h2 class="fw-bold mb-2">Model Terintegrasi AHP + SMART</h2>
                    <p class="mb-4 text-light opacity-90" style="max-width: 680px; font-size: 1.02rem;">
                        Pengambilan keputusan pemilihan siswa berprestasi objektif dan ilmiah. Kelemahan bobot manual subjektif pada jurnal acuan diperbaiki melalui <strong>Analytic Hierarchy Process (AHP)</strong> dengan uji konsistensi matriks Saaty, lalu diranking dengan <strong>SMART (Simple Multi Attribute Rating Technique)</strong>.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('students.index') }}" class="btn btn-light text-primary fw-semibold px-4 py-2 rounded-pill shadow-sm">
                            <i class="bi bi-person-plus-fill me-1"></i> Input Alternatif Siswa
                        </a>
                        <a href="{{ route('assessments.index') }}" class="btn btn-outline-light fw-semibold px-4 py-2 rounded-pill">
                            <i class="bi bi-pencil-fill me-1"></i> Input Penilaian Siswa
                        </a>
                        <a href="{{ route('smart.index') }}" class="btn btn-warning text-dark fw-semibold px-4 py-2 rounded-pill shadow-sm">
                            <i class="bi bi-trophy-fill me-1"></i> Lihat Hasil Perangkingan
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center d-none d-lg-block">
                    <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-20 backdrop-blur">
                        <i class="bi bi-award text-warning" style="font-size: 5rem;"></i>
                        <h6 class="text-white mt-2 mb-0 fw-bold">Rekomendasi Berprestasi</h6>
                        <small class="text-white-50">Transparan, Terukur & Konsisten</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 stat-card" style="background: #ffffff;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Kriteria Penilaian</div>
                        <h3 class="fw-bold my-1 text-primary">{{ $totalCriteria }} Kriteria</h3>
                        <small class="text-muted">K1 s.d K5 (Benefit)</small>
                    </div>
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-sliders"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 stat-card" style="background: #ffffff;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Alternatif Siswa</div>
                        <h3 class="fw-bold my-1 text-info">{{ $totalStudents }} Siswa</h3>
                        <small class="text-muted">Data Jurnal: A1 - A5</small>
                    </div>
                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 stat-card" style="background: #ffffff;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Status Konsistensi AHP</div>
                        <h3 class="fw-bold my-1 {{ ($activeAhp && $activeAhp->is_consistent) ? 'text-success' : 'text-danger' }}">
                            CR = {{ $activeAhp ? number_format($activeAhp->cr, 4) : 'N/A' }}
                        </h3>
                        <span class="badge {{ ($activeAhp && $activeAhp->is_consistent) ? 'badge-consistent' : 'badge-inconsistent' }}">
                            <i class="bi bi-check-circle-fill me-1"></i> {{ ($activeAhp && $activeAhp->is_consistent) ? 'KONSISTEN (CR < 0.1)' : 'TIDAK KONSISTEN' }}
                        </span>
                    </div>
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 stat-card" style="background: #ffffff;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Periode Penilaian</div>
                        <h3 class="fw-bold my-1 text-dark fs-5">{{ $activePeriod->nama ?? '2024/2025' }}</h3>
                        <small class="text-muted">Semester: <strong>{{ $activePeriod->semester ?? 'Ganjil' }}</strong></small>
                    </div>
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-calendar3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 1: Flowchart Model Terintegrasi & Preview Ranking -->
    <div class="row g-4 mb-4">
        <!-- Flowchart Interaktif Model Terintegrasi (Gambar 1.3) -->
        <div class="col-xl-7">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-diagram-3-fill text-primary me-2"></i>1.3 Alur Model Terintegrasi (AHP + SMART)
                    </h5>
                    <a href="{{ route('documentation.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-info-circle me-1"></i> Pelajari Formula
                    </a>
                </div>
                <div class="card-body p-4 bg-light bg-opacity-50">
                    <div class="row g-3">
                        <!-- Jalur AHP (Kiri) -->
                        <div class="col-md-6 border-end pe-md-3">
                            <div class="text-center text-primary fw-bold small text-uppercase mb-2">
                                <i class="bi bi-1-circle-fill me-1"></i> Tahap AHP (Penentuan Bobot)
                            </div>
                            
                            <div class="flowchart-box bg-white">
                                <i class="bi bi-list-check text-primary me-1"></i> Tentukan Kriteria ($n=5$)
                            </div>
                            <div class="flowchart-arrow"><i class="bi bi-arrow-down"></i></div>

                            <div class="flowchart-box bg-white">
                                Matriks Perbandingan Berpasangan AHP<br>
                                <span class="badge bg-secondary-subtle text-dark">Skala Saaty 1 – 9</span>
                            </div>
                            <div class="flowchart-arrow"><i class="bi bi-arrow-down"></i></div>

                            <div class="flowchart-box bg-white">
                                Hitung Bobot Prioritas / Eigenvector ($w_j$)
                            </div>
                            <div class="flowchart-arrow"><i class="bi bi-arrow-down"></i></div>

                            <div class="p-3 text-center rounded-3 border {{ ($activeAhp && $activeAhp->is_consistent) ? 'bg-success-subtle border-success' : 'bg-warning-subtle border-warning' }}">
                                <div class="fw-bold">Uji Konsistensi: CR &lt; 0,1 ?</div>
                                <div class="small text-muted mt-1">
                                    $\lambda_{max} = {{ $activeAhp->lambda_max ?? 5.0331 }}$, CR = <strong>{{ $activeAhp->cr ?? 0.0074 }}</strong>
                                </div>
                                <span class="badge bg-success mt-1">Ya (Konsisten)</span>
                            </div>
                            <div class="flowchart-arrow"><i class="bi bi-arrow-down"></i></div>

                            <div class="flowchart-box bg-primary text-white border-primary">
                                Bobot AHP = Bobot Ternormalisasi SMART ($w_j$)
                            </div>
                        </div>

                        <!-- Jalur SMART (Kanan) -->
                        <div class="col-md-6 ps-md-3">
                            <div class="text-center text-info fw-bold small text-uppercase mb-2">
                                <i class="bi bi-2-circle-fill me-1"></i> Tahap SMART (Penilaian Alternatif)
                            </div>

                            <div class="flowchart-box bg-white">
                                Input Data Siswa (Alternatif) + Skor Parameter ($C_{out}$)
                            </div>
                            <div class="flowchart-arrow"><i class="bi bi-arrow-down"></i></div>

                            <div class="flowchart-box bg-white">
                                Hitung Nilai Utility SMART ($u_j$):<br>
                                <code class="small text-primary">u = (Cout - Cmin)/(Cmax - Cmin) × 100</code>
                            </div>
                            <div class="flowchart-arrow"><i class="bi bi-arrow-down"></i></div>

                            <!-- Integrasi -->
                            <div class="flowchart-box bg-dark text-white border-dark p-3 mt-4">
                                <div class="small text-uppercase text-warning fw-bold mb-1">Integrasi Model</div>
                                <div class="fs-6 fw-bold">Nilai Akhir = $\sum w_j \cdot u_j$</div>
                                <div class="small opacity-75">Perkalian Bobot AHP dengan Utility SMART</div>
                            </div>
                            <div class="flowchart-arrow"><i class="bi bi-arrow-down"></i></div>

                            <div class="flowchart-box bg-white">
                                Perankingan Nilai Terbesar ke Terkecil
                            </div>
                            <div class="flowchart-arrow"><i class="bi bi-arrow-down"></i></div>

                            <div class="flowchart-box bg-warning text-dark border-warning fw-bold">
                                <i class="bi bi-trophy-fill text-warning me-1"></i> Rekomendasi Siswa Berprestasi
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Top Siswa Berprestasi -->
        <div class="col-xl-5">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-trophy-fill text-warning me-2"></i>Peringkat Siswa Berprestasi
                    </h5>
                    <a href="{{ route('smart.index') }}" class="btn btn-sm btn-outline-primary">
                        Rincian Lengkap <i class="bi bi-chevron-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-custom table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3" style="width: 70px;">Rank</th>
                                    <th>Kode / Siswa</th>
                                    <th class="text-center">Nilai Akhir</th>
                                    <th class="text-end pe-3">Rekomendasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rankedPreview as $res)
                                <tr>
                                    <td class="ps-3">
                                        @if($res['ranking'] == 1)
                                            <span class="badge bg-warning text-dark rounded-circle p-2 fs-6 shadow-sm"><i class="bi bi-award-fill"></i></span>
                                        @elseif($res['ranking'] == 2)
                                            <span class="badge bg-secondary text-white rounded-circle p-2 fs-6 shadow-sm">2</span>
                                        @elseif($res['ranking'] == 3)
                                            <span class="badge bg-bronze text-white rounded-circle p-2 fs-6 shadow-sm" style="background-color: #cd7f32;">3</span>
                                        @else
                                            <span class="badge bg-light text-muted border rounded-circle p-2">{{ $res['ranking'] }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $res['nama'] }}</div>
                                        <div class="small text-muted">{{ $res['kode'] }} &bull; {{ $res['student']->kelas ?? 'XII RPL 1' }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary fw-bold fs-6 px-3 py-2 rounded-pill">
                                            {{ number_format($res['nilai_akhir'], 2) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <span class="badge {{ $res['badge_class'] ?? 'bg-info' }} rounded-pill px-2 py-1">
                                            {{ $res['rekomendasi'] }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        Belum ada data penilaian siswa. Silakan lakukan input penilaian.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top p-3 text-center">
                    <a href="{{ route('reports.print') }}" target="_blank" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                        <i class="bi bi-printer me-1"></i> Cetak Laporan Rekomendasi Resmi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Steps -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h6 class="fw-bold text-uppercase text-muted small mb-3">Langkah Mudah Operasional Sistem</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="p-3 border rounded-3 bg-white h-100">
                        <div class="badge bg-primary mb-2">Langkah 1</div>
                        <h6 class="fw-bold mb-1">Tetapkan Kriteria</h6>
                        <p class="small text-muted mb-2">5 Kriteria baku rapor dan kepribadian siswa (Tabel 1 Jurnal).</p>
                        <a href="{{ route('criteria.index') }}" class="btn btn-sm btn-light text-primary w-100">Kelola Kriteria</a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 border rounded-3 bg-white h-100">
                        <div class="badge bg-primary mb-2">Langkah 2</div>
                        <h6 class="fw-bold mb-1">Input Alternatif Siswa</h6>
                        <p class="small text-muted mb-2">Data siswa peserta seleksi (A1 s.d A5 dari jurnal atau siswa baru).</p>
                        <a href="{{ route('students.index') }}" class="btn btn-sm btn-light text-primary w-100">Kelola Siswa</a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 border rounded-3 bg-white h-100">
                        <div class="badge bg-primary mb-2">Langkah 3</div>
                        <h6 class="fw-bold mb-1">Perbandingan AHP</h6>
                        <p class="small text-muted mb-2">Isi matriks Saaty 1-9 & verifikasi nilai konsistensi CR &lt; 0.1.</p>
                        <a href="{{ route('ahp.index') }}" class="btn btn-sm btn-light text-primary w-100">Modul AHP</a>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 border rounded-3 bg-white h-100">
                        <div class="badge bg-primary mb-2">Langkah 4</div>
                        <h6 class="fw-bold mb-1">Input Nilai & Ranking SMART</h6>
                        <p class="small text-muted mb-2">Input skor parameter dan hitung utility untuk melihat pemenang.</p>
                        <a href="{{ route('smart.index') }}" class="btn btn-sm btn-primary w-100">Perangkingan SMART</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
