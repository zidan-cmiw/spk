@extends('layouts.app')

@section('title', 'Bobot Kriteria AHP')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-info text-dark fs-6 px-3 py-1 rounded-pill fw-bold">Langkah 3</span>
                <h4 class="fw-bold mb-0">Bobot Kriteria (Metode AHP)</h4>
            </div>
            <p class="text-muted small mt-1 mb-0">Bobot kriteria ditentukan dengan perbandingan berpasangan Saaty dan wajib konsisten ($CR < 0,1$).</p>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('ahp.preset') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success btn-sm px-4 rounded-pill fw-bold shadow-sm">
                    <i class="bi bi-magic me-1"></i> Terapkan Bobot Standar AHP
                </button>
            </form>
            <a href="{{ route('smart.index') }}" class="btn btn-warning text-dark btn-sm px-4 rounded-pill fw-bold shadow-sm">
                Lihat Ranking Juara <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    <!-- Status Konsistensi Simpel & Kartu Bobot -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100 p-3 bg-white border-2 {{ $ahpResult['is_consistent'] ? 'border-success' : 'border-danger' }} shadow-sm text-center d-flex flex-column justify-content-center">
                <div class="fs-1 {{ $ahpResult['is_consistent'] ? 'text-success' : 'text-danger' }}">
                    <i class="bi {{ $ahpResult['is_consistent'] ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }}"></i>
                </div>
                <h5 class="fw-bold mt-2 mb-1">
                    {{ $ahpResult['is_consistent'] ? 'Bobot Konsisten' : 'Inkonsisten' }}
                </h5>
                <span class="badge {{ $ahpResult['is_consistent'] ? 'bg-success' : 'bg-danger' }} fs-6 px-3 py-1 rounded-pill mx-auto">
                    Nilai CR = {{ $ahpResult['cr'] }} (&lt; 0.1)
                </span>
                <small class="text-muted mt-2">
                    {{ $ahpResult['is_consistent'] ? 'Bobot valid dan aktif digunakan pada ranking.' : 'Perlu disesuaikan agar CR < 0.1.' }}
                </small>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card h-100 p-4 bg-white shadow-sm">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Persentase Bobot Kriteria Saat Ini:</h6>
                <div class="row g-2 text-center">
                    @foreach($criteria as $i => $c)
                    @php $w = $ahpResult['weights'][$i]; @endphp
                    <div class="col">
                        <div class="p-2 border rounded-3 bg-light">
                            <span class="badge bg-primary mb-1">{{ $c->kode }}</span>
                            <div class="fw-bold small text-dark">{{ $c->nama }}</div>
                            <div class="fs-5 fw-bold text-primary mt-1">{{ number_format($w * 100, 1) }}%</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Kuesioner Perbandingan Sederhana -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold"><i class="bi bi-sliders text-primary me-2"></i>Atur Perbandingan Kepentingan Kriteria</h6>
            <small class="text-muted">Pilih tingkat kepentingan antara kriteria di bawah ini jika ingin mengubah:</small>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('ahp.calculate') }}" method="POST">
                @csrf
                <input type="hidden" name="nama_sesi" value="Sesi AHP Prestasi Siswa">

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start" style="width: 25%;">Kriteria A</th>
                                <th style="width: 50%;">Tingkat Kepentingan Relatif</th>
                                <th class="text-end" style="width: 25%;">Kriteria B</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $critList = $criteria->values();
                                $n = count($critList);
                            @endphp

                            @for($i = 0; $i < $n; $i++)
                                @for($j = $i + 1; $j < $n; $j++)
                                    @php
                                        $idA = $critList[$i]->id;
                                        $idB = $critList[$j]->id;
                                        $pairKey = $idA . '_' . $idB;
                                        $currentVal = $matrix[$i][$j] ?? 1.0;
                                    @endphp
                                    <tr>
                                        <td class="text-start">
                                            <span class="badge bg-primary me-1">{{ $critList[$i]->kode }}</span>
                                            <strong>{{ $critList[$i]->nama }}</strong>
                                        </td>
                                        <td>
                                            <select name="pair[{{ $pairKey }}]" class="form-select form-select-sm text-center fw-bold text-primary mx-auto" style="max-width: 280px;">
                                                <option value="9" {{ abs($currentVal - 9) < 0.01 ? 'selected' : '' }}>9 - Mutlak Lebih Penting dari</option>
                                                <option value="7" {{ abs($currentVal - 7) < 0.01 ? 'selected' : '' }}>7 - Sangat Lebih Penting dari</option>
                                                <option value="5" {{ abs($currentVal - 5) < 0.01 ? 'selected' : '' }}>5 - Lebih Penting dari</option>
                                                <option value="3" {{ abs($currentVal - 3) < 0.01 ? 'selected' : '' }}>3 - Sedikit Lebih Penting dari</option>
                                                <option value="2" {{ abs($currentVal - 2) < 0.01 ? 'selected' : '' }}>2 - Mendekati Sedikit Lebih Penting</option>
                                                <option value="1" {{ abs($currentVal - 1) < 0.01 ? 'selected' : '' }}>1 - Sama Penting dengan</option>
                                                <option value="0.5" {{ abs($currentVal - 0.5) < 0.01 ? 'selected' : '' }}>1/2 - Sebaliknya (Lebih Rendah)</option>
                                                <option value="0.3333" {{ abs($currentVal - (1/3)) < 0.01 ? 'selected' : '' }}>1/3 - Sebaliknya (Sedikit Lebih Rendah)</option>
                                                <option value="0.25" {{ abs($currentVal - 0.25) < 0.01 ? 'selected' : '' }}>1/4 - Sebaliknya</option>
                                                <option value="0.2" {{ abs($currentVal - 0.2) < 0.01 ? 'selected' : '' }}>1/5 - Sebaliknya</option>
                                            </select>
                                        </td>
                                        <td class="text-end">
                                            <strong>{{ $critList[$j]->nama }}</strong>
                                            <span class="badge bg-secondary ms-1">{{ $critList[$j]->kode }}</span>
                                        </td>
                                    </tr>
                                @endfor
                            @endfor
                        </tbody>
                    </table>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">
                        <i class="bi bi-calculator me-1"></i> Hitung & Simpan Bobot Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
