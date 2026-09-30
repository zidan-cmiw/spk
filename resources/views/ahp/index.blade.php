@extends('layouts.app')

@section('title', 'Modul AHP')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-diagram-3-fill text-primary me-2"></i>Modul Analytic Hierarchy Process (AHP)</h4>
            <p class="text-muted mb-0 small">Penentuan bobot prioritas kriteria ilmiah berbasis perbandingan berpasangan (Pairwise Comparison) skala Saaty 1 – 9 dan uji konsistensi (CR &lt; 0.1).</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <form action="{{ route('ahp.preset') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm px-3 rounded-pill shadow-sm">
                    <i class="bi bi-magic me-1"></i> Gunakan Preset Konsisten Jurnal (CR = 0.0074)
                </button>
            </form>
            <a href="{{ route('smart.index') }}" class="btn btn-warning btn-sm px-3 rounded-pill text-dark fw-semibold shadow-sm">
                <i class="bi bi-trophy-fill me-1"></i> Terapkan ke SMART
            </a>
        </div>
    </div>

    <!-- Status Konsistensi Banner -->
    <div class="card mb-4 border-0 {{ $ahpResult['is_consistent'] ? 'bg-success text-white' : 'bg-danger text-white' }} shadow-sm">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3">
                        <div class="fs-1">
                            <i class="bi {{ $ahpResult['is_consistent'] ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }}"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">
                                Status Uji Konsistensi: 
                                {{ $ahpResult['is_consistent'] ? 'KONSISTEN (CR < 0,1)' : 'TIDAK KONSISTEN (CR ≥ 0,1)' }}
                            </h5>
                            <p class="mb-0 opacity-90 small">
                                @if($ahpResult['is_consistent'])
                                    Matriks perbandingan berpasangan memenuhi syarat konsistensi Saaty dengan <strong>CR = {{ $ahpResult['cr'] }} (&lt; 0,1)</strong>. Bobot prioritas valid dan siap diintegrasikan sebagai bobot ternormalisasi pada metode SMART.
                                @else
                                    Matriks perbandingan memiliki <strong>CR = {{ $ahpResult['cr'] }} (&ge; 0,1)</strong>. Menurut teori Saaty, penilaian dianggap inkonsisten sehingga matriks perbandingan berpasangan perlu ditinjau ulang.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <div class="bg-white bg-opacity-20 p-3 rounded-3 d-inline-block text-start">
                        <div class="small fw-semibold">&lambda; Max: <strong>{{ $ahpResult['lambda_max'] }}</strong></div>
                        <div class="small fw-semibold">Consistency Index (CI): <strong>{{ $ahpResult['ci'] }}</strong></div>
                        <div class="small fw-semibold">Random Index (RI n={{ count($criteria) }}): <strong>{{ $ahpResult['ri'] }}</strong></div>
                        <div class="small fw-bold">Consistency Ratio (CR): <span class="badge bg-white text-dark">{{ $ahpResult['cr'] }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Input Matriks Perbandingan Berpasangan -->
    <div class="card mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <div>
                <h6 class="mb-0 fw-bold"><i class="bi bi-ui-radios-grid me-2 text-primary"></i>Kuesioner Perbandingan Berpasangan Skala Saaty (1 – 9)</h6>
                <small class="text-muted">Tentukan tingkat kepentingan relatif kriteria A dibandingkan kriteria B.</small>
            </div>
            <a class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse" href="#saatyScaleGuide">
                <i class="bi bi-info-circle me-1"></i> Skala Saaty
            </a>
        </div>

        <div class="collapse" id="saatyScaleGuide">
            <div class="p-3 bg-light border-bottom small">
                <h6 class="fw-bold mb-2">Panduan Skala Perbandingan Saaty:</h6>
                <div class="row g-2">
                    <div class="col-md-4"><strong>1:</strong> Sama penting (Equal importance)</div>
                    <div class="col-md-4"><strong>3:</strong> Sedikit lebih penting (Moderate importance)</div>
                    <div class="col-md-4"><strong>5:</strong> Lebih penting (Strong importance)</div>
                    <div class="col-md-4"><strong>7:</strong> Sangat lebih penting (Very strong importance)</div>
                    <div class="col-md-4"><strong>9:</strong> Mutlak lebih penting (Extreme importance)</div>
                    <div class="col-md-4"><strong>2, 4, 6, 8:</strong> Nilai antara pertimbangan yang berdekatan</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('ahp.calculate') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nama Sesi Perhitungan AHP</label>
                    <input type="text" name="nama_sesi" class="form-control" value="Sesi AHP Prestasi SMK Mandiri - {{ date('d M Y') }}" required>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start" style="width: 25%;">Kriteria A</th>
                                <th style="width: 50%;">Skala Kepentingan (Saaty)</th>
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
                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                <select name="pair[{{ $pairKey }}]" class="form-select form-select-sm text-center fw-bold text-primary" style="max-width: 280px;">
                                                    <option value="9" {{ abs($currentVal - 9) < 0.01 ? 'selected' : '' }}>9 - Mutlak Lebih Penting dari</option>
                                                    <option value="8" {{ abs($currentVal - 8) < 0.01 ? 'selected' : '' }}>8 - Mendekati Mutlak Lebih Penting</option>
                                                    <option value="7" {{ abs($currentVal - 7) < 0.01 ? 'selected' : '' }}>7 - Sangat Lebih Penting dari</option>
                                                    <option value="6" {{ abs($currentVal - 6) < 0.01 ? 'selected' : '' }}>6 - Mendekati Sangat Lebih Penting</option>
                                                    <option value="5" {{ abs($currentVal - 5) < 0.01 ? 'selected' : '' }}>5 - Lebih Penting dari</option>
                                                    <option value="4" {{ abs($currentVal - 4) < 0.01 ? 'selected' : '' }}>4 - Mendekati Lebih Penting</option>
                                                    <option value="3" {{ abs($currentVal - 3) < 0.01 ? 'selected' : '' }}>3 - Sedikit Lebih Penting dari</option>
                                                    <option value="2" {{ abs($currentVal - 2) < 0.01 ? 'selected' : '' }}>2 - Mendekati Sedikit Lebih Penting</option>
                                                    <option value="1" {{ abs($currentVal - 1) < 0.01 ? 'selected' : '' }}>1 - Sama Penting dengan</option>
                                                    <option value="0.5" {{ abs($currentVal - 0.5) < 0.01 ? 'selected' : '' }}>1/2 - Sebaliknya (0.5)</option>
                                                    <option value="0.3333" {{ abs($currentVal - (1/3)) < 0.01 ? 'selected' : '' }}>1/3 - Sebaliknya (0.333)</option>
                                                    <option value="0.25" {{ abs($currentVal - 0.25) < 0.01 ? 'selected' : '' }}>1/4 - Sebaliknya (0.25)</option>
                                                    <option value="0.2" {{ abs($currentVal - 0.2) < 0.01 ? 'selected' : '' }}>1/5 - Sebaliknya (0.2)</option>
                                                    <option value="0.1667" {{ abs($currentVal - (1/6)) < 0.01 ? 'selected' : '' }}>1/6 - Sebaliknya (0.167)</option>
                                                    <option value="0.1429" {{ abs($currentVal - (1/7)) < 0.01 ? 'selected' : '' }}>1/7 - Sebaliknya (0.143)</option>
                                                    <option value="0.125" {{ abs($currentVal - 0.125) < 0.01 ? 'selected' : '' }}>1/8 - Sebaliknya (0.125)</option>
                                                    <option value="0.1111" {{ abs($currentVal - (1/9)) < 0.01 ? 'selected' : '' }}>1/9 - Sebaliknya (0.111)</option>
                                                </select>
                                            </div>
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

                <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                    <span class="text-muted small">
                        <i class="bi bi-cpu-fill text-primary me-1"></i> Perhitungan otomatis menghasilkan matriks resiprokal $a_{ji} = 1 / a_{ij}$ dan menguji CR.
                    </span>
                    <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm">
                        <i class="bi bi-calculator me-1"></i> Hitung & Simpan Sesi AHP
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Rincian Matriks Perhitungan AHP (Step by Step) -->
    <div class="row g-4 mb-4">
        <!-- Matriks Perbandingan Berpasangan (A) -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-grid-3x3 me-2 text-primary"></i>Langkah 1: Matriks Perbandingan Berpasangan ($A$)</h6>
                    <small class="text-muted">Diagonal bernilai 1.000, elemen bawah diagonal adalah kebalikan (1/x).</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start">Kriteria</th>
                                    @foreach($criteria as $c)
                                    <th>{{ $c->kode }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($criteria as $i => $rowCrit)
                                <tr>
                                    <td class="text-start fw-bold table-light">{{ $rowCrit->kode }}</td>
                                    @foreach($criteria as $j => $colCrit)
                                    <td class="{{ $i == $j ? 'bg-light fw-bold text-muted' : '' }}">
                                        {{ number_format($ahpResult['matrix'][$i][$j], 3) }}
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td class="text-start">Total Kolom</td>
                                    @foreach($criteria as $j => $colCrit)
                                    <td class="text-primary">{{ number_format($ahpResult['col_sums'][$j], 3) }}</td>
                                    @endforeach
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matriks Ternormalisasi & Bobot Prioritas -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart-steps me-2 text-primary"></i>Langkah 2: Matriks Ternormalisasi & Bobot Prioritas ($w_i$)</h6>
                    <small class="text-muted">Normalisasi elemen dibagi total kolom, bobot adalah rata-rata tiap baris.</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start">Kriteria</th>
                                    @foreach($criteria as $c)
                                    <th>{{ $c->kode }}</th>
                                    @endforeach
                                    <th class="table-primary text-primary">Bobot ($w_i$)</th>
                                    <th class="table-info text-info">Persentase</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalWeight = 0; @endphp
                                @foreach($criteria as $i => $rowCrit)
                                @php
                                    $w = $ahpResult['weights'][$i];
                                    $totalWeight += $w;
                                @endphp
                                <tr>
                                    <td class="text-start fw-bold table-light">{{ $rowCrit->kode }}</td>
                                    @foreach($criteria as $j => $colCrit)
                                    <td>{{ number_format($ahpResult['norm_matrix'][$i][$j], 4) }}</td>
                                    @endforeach
                                    <td class="table-primary fw-bold text-primary">{{ number_format($w, 4) }}</td>
                                    <td class="table-info fw-bold text-info">{{ number_format($w * 100, 2) }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="{{ count($criteria) + 1 }}" class="text-end">Total Bobot:</td>
                                    <td class="table-primary text-primary">{{ number_format($totalWeight, 4) }}</td>
                                    <td class="table-info text-info">100%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Perbandingan Bobot Jurnal Manual vs Bobot Hasil AHP -->
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <div>
                <h6 class="mb-0 fw-bold"><i class="bi bi-arrow-left-right text-primary me-2"></i>Komparasi: Bobot Manual Jurnal Acuan vs Bobot AHP Terintegrasi</h6>
                <small class="text-muted">Bahan telaah kritis untuk presentasi ke dosen: pembuktian ilmiah dasar bobot.</small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th class="ps-3 text-start">Kode</th>
                            <th class="text-start">Nama Kriteria</th>
                            <th>Tipe</th>
                            <th>Bobot Manual Jurnal</th>
                            <th>Bobot Hasil AHP</th>
                            <th>Selisih Bobot</th>
                            <th class="text-end pe-3">Analisis Ilmiah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($criteria as $i => $crit)
                        @php
                            $jWeight = $crit->bobot_default;
                            $aWeight = $ahpResult['weights'][$i];
                            $diff = $aWeight - $jWeight;
                        @endphp
                        <tr>
                            <td class="ps-3 text-start">
                                <span class="badge bg-primary fw-bold">{{ $crit->kode }}</span>
                            </td>
                            <td class="text-start fw-bold">{{ $crit->nama }}</td>
                            <td><span class="badge bg-success-subtle text-success">Benefit</span></td>
                            <td>
                                <span class="badge bg-light text-dark border fs-6 px-3 py-1">
                                    {{ number_format($jWeight * 100, 1) }}% ({{ number_format($jWeight, 4) }})
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6 px-3 py-1 fw-bold">
                                    {{ number_format($aWeight * 100, 2) }}% ({{ number_format($aWeight, 4) }})
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold {{ $diff >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff * 100, 2) }}%
                                </span>
                            </td>
                            <td class="text-end pe-3 small text-muted">
                                @if($crit->kode == 'K1')
                                    Pengetahuan tetap prioritas utama (tertinggi).
                                @elseif($crit->kode == 'K2')
                                    Keterampilan kejuruan mendapat bobot lebih proporsional.
                                @elseif($crit->kode == 'K3' || $crit->kode == 'K4')
                                    Sikap & Kehadiran setara (1:1 perbandingan).
                                @else
                                    Ekstrakurikuler sebagai kriteria pendukung terstruktur.
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
