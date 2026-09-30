@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Hero Sederhana & Bersih -->
    <div class="card border-0 text-white mb-4 shadow-sm" style="background: linear-gradient(135deg, #3730a3 0%, #4f46e5 100%); border-radius: 16px;">
        <div class="card-body p-4 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <span class="badge bg-white text-primary fw-bold mb-2 px-3 py-1 rounded-pill">
                        Sistem Pendukung Keputusan Siswa Berprestasi
                    </span>
                    <h3 class="fw-bold mb-1">SMK Mandiri (Metode AHP + SMART)</h3>
                    <p class="mb-0 text-light opacity-90 small">
                        Hanya 3 langkah mudah: <strong>1. Tambah Siswa</strong> &rarr; <strong>2. Isi Nilai</strong> &rarr; <strong>3. Lihat Ranking Juara</strong>.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-success fs-6 px-3 py-2 rounded-pill d-flex align-items-center gap-1">
                        <i class="bi bi-check-circle-fill"></i> Bobot AHP: Konsisten (CR &lt; 0.1)
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3 LANGKAH UTAMA: SANGAT JELAS DI MANA HARUS INPUT -->
    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-play-circle-fill text-primary me-2"></i>Mulai Menggunakan Sistem:</h5>
    <div class="row g-3 mb-4">
        <!-- Langkah 1 -->
        <div class="col-md-4">
            <div class="card h-100 border-2 border-primary-subtle shadow-sm bg-white">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-primary fs-6 px-3 py-1 rounded-pill">Langkah 1</span>
                            <i class="bi bi-person-plus-fill text-primary fs-2"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Input Siswa (Alternatif)</h5>
                        <p class="text-muted small mb-3">
                            Tambahkan nama-nama siswa yang akan dinilai prestasinya.
                        </p>
                    </div>
                    <a href="{{ route('students.index') }}" class="btn btn-primary w-100 py-2 fw-bold rounded-pill">
                        <i class="bi bi-plus-circle me-1"></i> Buka Form Input Siswa
                    </a>
                </div>
            </div>
        </div>

        <!-- Langkah 2 -->
        <div class="col-md-4">
            <div class="card h-100 border-2 border-warning-subtle shadow-sm bg-white">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-warning text-dark fs-6 px-3 py-1 rounded-pill fw-bold">Langkah 2</span>
                            <i class="bi bi-pencil-square text-warning fs-2"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Input Nilai Siswa</h5>
                        <p class="text-muted small mb-3">
                            Pilih siswa dan masukkan nilai untuk 5 kriteria (Pengetahuan, Keterampilan, Sikap, Alpa, Ekskul).
                        </p>
                    </div>
                    <a href="{{ route('assessments.index') }}" class="btn btn-warning text-dark w-100 py-2 fw-bold rounded-pill">
                        <i class="bi bi-pencil-fill me-1"></i> Buka Form Input Nilai
                    </a>
                </div>
            </div>
        </div>

        <!-- Langkah 3 -->
        <div class="col-md-4">
            <div class="card h-100 border-2 border-success-subtle shadow-sm bg-white">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-success fs-6 px-3 py-1 rounded-pill">Langkah 3</span>
                            <i class="bi bi-trophy-fill text-warning fs-2"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Hasil Ranking Juara</h5>
                        <p class="text-muted small mb-3">
                            Lihat urutan ranking siswa berprestasi, grafik nilai, dan cetak surat keputusan.
                        </p>
                    </div>
                    <a href="{{ route('smart.index') }}" class="btn btn-success w-100 py-2 fw-bold rounded-pill">
                        <i class="bi bi-award-fill me-1"></i> Buka Hasil Ranking
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- PODIUM & PREVIEW TOP SISWA (BERSIH & CEPAT DIBACA) -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold"><i class="bi bi-trophy text-warning me-2"></i>Peringkat Siswa Saat Ini (Data Hasil Hitung)</h6>
            <a href="{{ route('smart.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                Lihat Semua Ranking <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Peringkat</th>
                            <th class="text-start">Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Nilai Akhir</th>
                            <th class="text-end pe-4">Status Rekomendasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rankedPreview as $res)
                        <tr>
                            <td>
                                @if($res['ranking'] == 1)
                                    <span class="badge bg-warning text-dark fs-6 px-3 py-1 rounded-pill fw-bold">
                                        <i class="bi bi-award-fill me-1"></i> Juara 1
                                    </span>
                                @elseif($res['ranking'] == 2)
                                    <span class="badge bg-secondary text-white fs-6 px-3 py-1 rounded-pill fw-bold">
                                        Juara 2
                                    </span>
                                @elseif($res['ranking'] == 3)
                                    <span class="badge bg-dark text-white fs-6 px-3 py-1 rounded-pill fw-bold">
                                        Juara 3
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border fs-6 px-3 py-1 rounded-pill">
                                        Rank #{{ $res['ranking'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-start">
                                <span class="badge bg-primary me-2">{{ $res['kode'] }}</span>
                                <strong class="text-dark">{{ $res['nama'] }}</strong>
                            </td>
                            <td>{{ $res['student']->kelas ?? 'XII RPL 1' }}</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary fs-6 fw-bold px-3 py-1">
                                    {{ number_format($res['nilai_akhir'], 2) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <span class="badge {{ $res['badge_class'] ?? 'bg-info' }} rounded-pill px-3 py-1">
                                    {{ $res['rekomendasi'] }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada penilaian. Silakan lakukan input nilai.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
