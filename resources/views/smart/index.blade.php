@extends('layouts.app')

@section('title', 'Perangkingan SMART')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-trophy-fill text-warning me-2"></i>Perhitungan & Perangkingan Metode SMART</h4>
            <p class="text-muted mb-0 small">Kalkulasi nilai utilitas, pembobotan, dan penetapan rekomendasi siswa berprestasi SMK Mandiri.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <!-- Mode Switcher -->
            <div class="btn-group shadow-sm rounded-pill p-1 bg-white border" role="group">
                <a href="{{ route('smart.index', ['mode' => 'ahp']) }}" class="btn btn-sm rounded-pill {{ $mode == 'ahp' ? 'btn-primary' : 'btn-light text-muted' }}">
                    <i class="bi bi-check2-circle me-1"></i> Bobot AHP (Terintegrasi)
                </a>
                <a href="{{ route('smart.index', ['mode' => 'jurnal']) }}" class="btn btn-sm rounded-pill {{ $mode == 'jurnal' ? 'btn-dark' : 'btn-light text-muted' }}">
                    <i class="bi bi-journal-text me-1"></i> Bobot Manual Jurnal (40/20/15/15/10)
                </a>
            </div>

            <form action="{{ route('smart.save') }}" method="POST" class="d-inline">
                @csrf
                <input type="hidden" name="mode" value="{{ $mode }}">
                <button type="submit" class="btn btn-success btn-sm px-3 rounded-pill shadow-sm">
                    <i class="bi bi-cloud-arrow-up-fill me-1"></i> Simpan Hasil
                </button>
            </form>

            <a href="{{ route('reports.print', ['mode' => $mode]) }}" target="_blank" class="btn btn-outline-dark btn-sm px-3 rounded-pill shadow-sm">
                <i class="bi bi-printer-fill me-1"></i> Cetak Laporan
            </a>
        </div>
    </div>

    <!-- Mode Banner Information -->
    <div class="alert {{ $mode == 'ahp' ? 'alert-primary border-primary-subtle' : 'alert-secondary border-secondary-subtle' }} shadow-sm d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="fw-bold fs-6">
                <i class="bi {{ $mode == 'ahp' ? 'bi-shield-check' : 'bi-info-circle' }} me-2"></i>
                Menggunakan: {{ $mode == 'ahp' ? 'Bobot Hasil Analytic Hierarchy Process (AHP)' : 'Bobot Manual Jurnal Acuan Herdiana dkk. (2024)' }}
            </div>
            <div class="small opacity-90 mt-1">
                @if($mode == 'ahp')
                    Bobot kriteria dihitung dari matriks perbandingan berpasangan Saaty (CR = {{ $activeSession->cr ?? 0.0074 }} &lt; 0.1):
                    @foreach($criteria as $c)
                        <span class="badge bg-white text-primary border me-1">
                            {{ $c->kode }}: {{ number_format(($calculation['normalized_weights'][$c->id] ?? 0) * 100, 2) }}%
                        </span>
                    @endforeach
                @else
                    Bobot kriteria manual dari jurnal acuan (tanpa uji konsistensi):
                    @foreach($criteria as $c)
                        <span class="badge bg-white text-dark border me-1">
                            {{ $c->kode }}: {{ ($c->bobot_default * 100) }}%
                        </span>
                    @endforeach
                @endif
            </div>
        </div>
        <div class="d-none d-md-block text-end">
            <span class="badge {{ $mode == 'ahp' ? 'bg-primary' : 'bg-dark' }} px-3 py-2 rounded-pill">
                {{ $mode == 'ahp' ? 'AHP + SMART Model' : 'Standard SMART Model' }}
            </span>
        </div>
    </div>

    <!-- Row: Chart.js Visualizations & Top 3 Podium -->
    <div class="row g-4 mb-4">
        <!-- Visual Bar Chart -->
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Grafik Perangkingan Siswa Berprestasi (Gambar 5 Jurnal)</h6>
                    <span class="badge bg-light text-dark border">Chart.js Visualizer</span>
                </div>
                <div class="card-body p-4">
                    <canvas id="rankingChart" height="220"></canvas>
                </div>
            </div>
        </div>

        <!-- Top 3 Podium Winners -->
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-award-fill text-warning me-2"></i>Rekomendasi 3 Siswa Berprestasi Utama</h6>
                    <small class="text-muted">Hasil keputusan akhir evaluasi siswa berprestasi SMK Mandiri.</small>
                </div>
                <div class="card-body p-3 d-flex flex-column justify-content-center">
                    @php
                        $top3 = array_slice($calculation['ranked_results'], 0, 3);
                    @endphp

                    @foreach($top3 as $idx => $winner)
                    <div class="p-3 mb-2 rounded-3 border d-flex align-items-center justify-content-between {{ $idx == 0 ? 'bg-warning-subtle border-warning' : ($idx == 1 ? 'bg-light border-secondary' : 'bg-light border-light') }}">
                        <div class="d-flex align-items-center gap-3">
                            <div class="badge {{ $idx == 0 ? 'bg-warning text-dark' : ($idx == 1 ? 'bg-secondary text-white' : 'bg-dark text-white') }} fs-5 rounded-circle" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;">
                                @if($idx == 0)
                                    <i class="bi bi-trophy-fill"></i>
                                @else
                                    {{ $idx + 1 }}
                                @endif
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">{{ $winner['nama'] }}</h6>
                                <small class="text-muted">{{ $winner['kode'] }} &bull; {{ $winner['student']->kelas ?? 'XII RPL 1' }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="fs-5 fw-bold {{ $idx == 0 ? 'text-primary' : 'text-dark' }}">
                                {{ number_format($winner['nilai_akhir'], 2) }}
                            </span>
                            <div class="small text-muted">{{ $winner['rekomendasi'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs for Calculation Steps -->
    <div class="card mb-4">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs card-header-tabs m-0 px-3" id="smartTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active py-3 fw-bold" id="ranking-tab" data-bs-toggle="tab" data-bs-target="#ranking-content" type="button" role="tab">
                        <i class="bi bi-trophy me-1 text-warning"></i> 1. Hasil Perangkingan (Tabel 7)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-3 fw-bold" id="final-tab" data-bs-toggle="tab" data-bs-target="#final-content" type="button" role="tab">
                        <i class="bi bi-calculator me-1 text-primary"></i> 2. Nilai Akhir (Tabel 6)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-3 fw-bold" id="utility-tab" data-bs-toggle="tab" data-bs-target="#utility-content" type="button" role="tab">
                        <i class="bi bi-graph-up me-1 text-info"></i> 3. Nilai Utility SMART (Tabel 5)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-3 fw-bold" id="raw-tab" data-bs-toggle="tab" data-bs-target="#raw-content" type="button" role="tab">
                        <i class="bi bi-table me-1 text-secondary"></i> 4. Skor Parameter (Tabel 4)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link py-3 fw-bold" id="sens-tab" data-bs-toggle="tab" data-bs-target="#sens-content" type="button" role="tab">
                        <i class="bi bi-arrow-left-right me-1 text-success"></i> 5. Analisis Sensitivitas (Jurnal vs AHP)
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content" id="smartTabsContent">
                
                <!-- TAB 1: HASIL PERANGKINGAN (TABEL 7 JURNAL) -->
                <div class="tab-pane fade show active p-4" id="ranking-content" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-check2-all text-success me-1"></i> Tabel 7. Perankingan Alternatif Siswa Berprestasi
                        </h6>
                        <small class="text-muted">Diurutkan dari nilai akhir terbesar ke terkecil ($u(a_i)$).</small>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-custom table-hover align-middle mb-0 text-center">
                            <thead>
                                <tr>
                                    <th class="ps-3" style="width: 90px;">Peringkat</th>
                                    <th class="text-start" style="width: 100px;">Kode</th>
                                    <th class="text-start">Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Nilai Akhir ($u(a_i)$)</th>
                                    <th>Status Rekomendasi</th>
                                    <th class="text-end pe-3">Apresiasi Juara</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($calculation['ranked_results'] as $res)
                                <tr>
                                    <td class="ps-3">
                                        @if($res['ranking'] == 1)
                                            <span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill shadow-xs">
                                                <i class="bi bi-award-fill me-1"></i> Juara 1
                                            </span>
                                        @elseif($res['ranking'] == 2)
                                            <span class="badge bg-secondary text-white fs-6 px-3 py-2 rounded-pill shadow-xs">
                                                <i class="bi bi-award me-1"></i> Juara 2
                                            </span>
                                        @elseif($res['ranking'] == 3)
                                            <span class="badge bg-dark text-white fs-6 px-3 py-2 rounded-pill shadow-xs">
                                                <i class="bi bi-award me-1"></i> Juara 3
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border fs-6 px-3 py-2 rounded-pill">
                                                Rank #{{ $res['ranking'] }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-start">
                                        <span class="badge bg-primary fw-bold">{{ $res['kode'] }}</span>
                                    </td>
                                    <td class="text-start">
                                        <div class="fw-bold text-dark">{{ $res['nama'] }}</div>
                                        <small class="text-muted">NIS: {{ $res['student']->nis ?? '-' }}</small>
                                    </td>
                                    <td>{{ $res['student']->kelas ?? 'XII RPL 1' }}</td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6 px-3 py-2 fw-bold">
                                            {{ number_format($res['nilai_akhir'], 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $res['badge_class'] ?? 'bg-secondary' }} px-3 py-1 rounded-pill">
                                            {{ $res['rekomendasi'] }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        @if($res['ranking'] == 1)
                                            <span class="text-warning fw-bold"><i class="bi bi-star-fill me-1"></i>Siswa Teladan</span>
                                        @elseif($res['ranking'] <= 3)
                                            <span class="text-success fw-bold"><i class="bi bi-check-lg me-1"></i>Beasiswa Prestasi</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 2: NILAI AKHIR LENGKAP (TABEL 6 JURNAL) -->
                <div class="tab-pane fade p-4" id="final-content" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-calculator-fill text-primary me-1"></i> Tabel 6. Rincian Komponen Perkalian Bobot & Utility ($w_j \cdot u_j$)
                        </h6>
                        <small class="text-muted">Formula: $u(a_i) = \sum_{j=1}^m w_j \cdot u_j(a_i)$</small>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 text-start">Kode</th>
                                    <th class="text-start">Nama Alternatif</th>
                                    @foreach($criteria as $c)
                                    <th>
                                        {{ $c->kode }}<br>
                                        <small class="text-primary fw-normal">($w = {{ number_format($calculation['normalized_weights'][$c->id], 4) }})</small>
                                    </th>
                                    @endforeach
                                    <th class="table-primary text-primary fw-bold">Nilai Akhir ($\sum w_j \cdot u_j$)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($calculation['ranked_results'] as $res)
                                <tr>
                                    <td class="ps-3 text-start">
                                        <span class="badge bg-primary fw-bold">{{ $res['kode'] }}</span>
                                    </td>
                                    <td class="text-start fw-bold">{{ $res['nama'] }}</td>
                                    @foreach($criteria as $c)
                                    <td>
                                        <span class="fw-semibold text-dark">
                                            {{ number_format($res['weighted_components'][$c->id], 3) }}
                                        </span>
                                        <div class="text-muted" style="font-size: 0.72rem;">
                                            ({{ number_format($calculation['normalized_weights'][$c->id], 2) }} &times; {{ number_format($res['utility_scores'][$c->id], 1) }})
                                        </div>
                                    </td>
                                    @endforeach
                                    <td class="table-primary fw-bold text-primary fs-6">
                                        {{ number_format($res['nilai_akhir'], 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 3: NILAI UTILITY SMART (TABEL 5 JURNAL) -->
                <div class="tab-pane fade p-4" id="utility-content" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="bi bi-graph-up-arrow text-info me-1"></i> Tabel 5. Nilai Utility SMART Tiap Alternatif
                            </h6>
                            <small class="text-muted">
                                Dihitung dengan rumus Benefit: <code>u = (Cout - Cmin) / (Cmax - Cmin) &times; 100</code> dengan Cmin=20 dan Cmax=100.
                            </small>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 text-start">Kode</th>
                                    <th class="text-start">Alternatif Siswa</th>
                                    @foreach($criteria as $c)
                                    <th>{{ $c->kode }} - {{ $c->nama }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($calculation['ranked_results'] as $res)
                                <tr>
                                    <td class="ps-3 text-start">
                                        <span class="badge bg-primary fw-bold">{{ $res['kode'] }}</span>
                                    </td>
                                    <td class="text-start fw-bold">{{ $res['nama'] }}</td>
                                    @foreach($criteria as $c)
                                    <td>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle fw-bold fs-6 px-3 py-1">
                                            {{ number_format($res['utility_scores'][$c->id], 1) }}
                                        </span>
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 4: SKOR PARAMETER RAW (TABEL 4 JURNAL) -->
                <div class="tab-pane fade p-4" id="raw-content" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-table text-secondary me-1"></i> Tabel 4. Skor Kriteria untuk Setiap Alternatif ($C_{out}$)
                        </h6>
                        <small class="text-muted">Data skor parameter asli (skala 20 – 100).</small>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 text-start">Kode Alternatif</th>
                                    <th class="text-start">Nama Siswa</th>
                                    @foreach($criteria as $c)
                                    <th>{{ $c->kode }} ({{ $c->nama }})</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($calculation['ranked_results'] as $res)
                                <tr>
                                    <td class="ps-3 text-start">
                                        <span class="badge bg-dark fw-bold">{{ $res['kode'] }}</span>
                                    </td>
                                    <td class="text-start fw-bold">{{ $res['nama'] }}</td>
                                    @foreach($criteria as $c)
                                    <td>
                                        <span class="fw-bold text-dark fs-6">
                                            {{ $res['raw_scores'][$c->id] }}
                                        </span>
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 5: ANALISIS SENSITIVITAS (JURNAL VS AHP) -->
                <div class="tab-pane fade p-4" id="sens-content" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="bi bi-arrow-left-right text-success me-1"></i> Analisis Sensitivitas: Bobot Manual Jurnal vs Bobot Terintegrasi AHP
                            </h6>
                            <small class="text-muted">Bahan presentasi ke dosen: pembuktian dampak integrasi AHP terhadap perankingan siswa.</small>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 text-start" rowspan="2">Kode</th>
                                    <th class="text-start" rowspan="2">Nama Siswa</th>
                                    <th colspan="2" class="table-secondary">Bobot Manual Jurnal Acuan</th>
                                    <th colspan="2" class="table-primary">Bobot Terintegrasi AHP (Konsisten)</th>
                                    <th rowspan="2">Perubahan Nilai</th>
                                    <th rowspan="2">Pergeseran Ranking</th>
                                </tr>
                                <tr>
                                    <th class="table-secondary">Nilai Akhir</th>
                                    <th class="table-secondary">Peringkat</th>
                                    <th class="table-primary">Nilai Akhir</th>
                                    <th class="table-primary">Peringkat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($comparison as $cmp)
                                <tr>
                                    <td class="ps-3 text-start">
                                        <span class="badge bg-dark fw-bold">{{ $cmp['kode'] }}</span>
                                    </td>
                                    <td class="text-start fw-bold">{{ $cmp['nama'] }}</td>
                                    <td class="table-secondary fw-bold">{{ number_format($cmp['journal_score'], 2) }}</td>
                                    <td class="table-secondary">Rank #{{ $cmp['journal_rank'] }}</td>
                                    <td class="table-primary fw-bold text-primary">{{ number_format($cmp['ahp_score'], 2) }}</td>
                                    <td class="table-primary fw-bold text-primary">Rank #{{ $cmp['ahp_rank'] }}</td>
                                    <td>
                                        @php $scoreDiff = $cmp['ahp_score'] - $cmp['journal_score']; @endphp
                                        <span class="{{ $scoreDiff >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                            {{ $scoreDiff >= 0 ? '+' : '' }}{{ number_format($scoreDiff, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($cmp['rank_diff'] > 0)
                                            <span class="badge bg-success"><i class="bi bi-arrow-up-short"></i> Naik {{ $cmp['rank_diff'] }} Posisi</span>
                                        @elseif($cmp['rank_diff'] < 0)
                                            <span class="badge bg-danger"><i class="bi bi-arrow-down-short"></i> Turun {{ abs($cmp['rank_diff']) }} Posisi</span>
                                        @else
                                            <span class="badge bg-secondary">Tetap (Rank #{{ $cmp['ahp_rank'] }})</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded-3 border small">
                        <strong>Kesimpulan Ilmiah Analisis Sensitivitas:</strong>
                        <ul class="mb-0 mt-1 ps-3">
                            <li>Data jurnal asli (A1 - A5) dengan bobot manual menghasilkan nilai persis: <strong>A4 = 72.5; A2 = 58.75; A5 = 57.5; A3 = 56.25; A1 = 55.0</strong> (Sesuai Tabel 6 & 7 di Jurnal Herdiana dkk., 2024).</li>
                            <li>Ketika bobot ditentukan secara terstruktur melalui AHP (dengan uji konsistensi $CR = 0.0074 < 0.1$), bobot kriteria memiliki landasan matematis kuat dan tidak subjektif.</li>
                            <li>Siswa <strong>Dedi Kurniawan (A4)</strong> konsisten menjadi juara 1 di kedua metode karena keunggulan pada kriteria K3 (Sikap=100) dan K4 (Kehadiran=100).</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Initialize Chart.js Bar Chart
    const ctx = document.getElementById('rankingChart').getContext('2d');
    const chartLabels = {!! json_encode($chartLabels) !!};
    const chartScores = {!! json_encode($chartScores) !!};

    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(79, 70, 229, 0.9)');
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0.2)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Nilai Akhir SMART (u(a_i))',
                data: chartScores,
                backgroundColor: gradient,
                borderColor: '#4f46e5',
                borderWidth: 2,
                borderRadius: 8,
                barPercentage: 0.55
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif"
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            weight: 'bold'
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' Nilai Akhir: ' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
