@extends('layouts.app')

@section('title', 'Hasil Ranking Juara')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success fs-6 px-3 py-1 rounded-pill fw-bold">Langkah 4</span>
                <h4 class="fw-bold mb-0">Hasil Ranking Siswa Berprestasi</h4>
            </div>
            <p class="text-muted small mt-1 mb-0">Peringkat siswa berprestasi SMK Mandiri berdasarkan perhitungan metode SMART.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <!-- Mode Toggle Sederhana -->
            <div class="btn-group bg-white border p-1 rounded-pill shadow-xs" role="group">
                <a href="{{ route('smart.index', ['mode' => 'ahp']) }}" class="btn btn-sm rounded-pill {{ $mode == 'ahp' ? 'btn-primary fw-bold' : 'btn-light text-muted' }}">
                    <i class="bi bi-check2-circle me-1"></i> Bobot AHP (Rekomendasi)
                </a>
                <a href="{{ route('smart.index', ['mode' => 'jurnal']) }}" class="btn btn-sm rounded-pill {{ $mode == 'jurnal' ? 'btn-dark fw-bold' : 'btn-light text-muted' }}">
                    <i class="bi bi-journal me-1"></i> Bobot Jurnal
                </a>
            </div>

            <a href="{{ route('reports.print', ['mode' => $mode]) }}" target="_blank" class="btn btn-outline-dark btn-sm px-4 rounded-pill fw-bold shadow-sm">
                <i class="bi bi-printer-fill me-1"></i> Cetak Laporan / PDF
            </a>
        </div>
    </div>

    <!-- PODIUM JUARA 1, 2, 3 (VISUAL & JELAS) -->
    @php
        $top3 = array_slice($calculation['ranked_results'], 0, 3);
    @endphp

    <div class="row g-3 mb-4">
        <!-- Juara 2 -->
        @if(isset($top3[1]))
        <div class="col-md-4 order-2 order-md-1">
            <div class="card text-center p-3 border-0 shadow-sm bg-white h-100">
                <div class="mb-2">
                    <span class="badge bg-secondary fs-6 px-3 py-1 rounded-pill">Juara 2</span>
                </div>
                <div class="fs-1 text-secondary"><i class="bi bi-award-fill"></i></div>
                <h5 class="fw-bold text-dark mt-2 mb-1">{{ $top3[1]['nama'] }}</h5>
                <div class="text-muted small mb-2">{{ $top3[1]['kode'] }} &bull; {{ $top3[1]['student']->kelas ?? 'XII RPL 1' }}</div>
                <div class="fs-4 fw-bold text-secondary">{{ number_format($top3[1]['nilai_akhir'], 2) }}</div>
            </div>
        </div>
        @endif

        <!-- Juara 1 -->
        @if(isset($top3[0]))
        <div class="col-md-4 order-1 order-md-2">
            <div class="card text-center p-4 border-2 border-warning shadow-sm bg-warning-subtle h-100">
                <div class="mb-2">
                    <span class="badge bg-warning text-dark fs-6 px-4 py-1 rounded-pill fw-bold">JUARA 1 (TERBAIK)</span>
                </div>
                <div class="display-5 text-warning"><i class="bi bi-trophy-fill"></i></div>
                <h4 class="fw-bold text-dark mt-2 mb-1">{{ $top3[0]['nama'] }}</h4>
                <div class="text-muted small mb-3">{{ $top3[0]['kode'] }} &bull; {{ $top3[0]['student']->kelas ?? 'XII RPL 1' }}</div>
                <div class="display-6 fw-bold text-primary">{{ number_format($top3[0]['nilai_akhir'], 2) }}</div>
                <small class="text-success fw-bold mt-1">Sangat Direkomendasikan</small>
            </div>
        </div>
        @endif

        <!-- Juara 3 -->
        @if(isset($top3[2]))
        <div class="col-md-4 order-3 order-md-3">
            <div class="card text-center p-3 border-0 shadow-sm bg-white h-100">
                <div class="mb-2">
                    <span class="badge bg-dark text-white fs-6 px-3 py-1 rounded-pill">Juara 3</span>
                </div>
                <div class="fs-1 text-dark"><i class="bi bi-award"></i></div>
                <h5 class="fw-bold text-dark mt-2 mb-1">{{ $top3[2]['nama'] }}</h5>
                <div class="text-muted small mb-2">{{ $top3[2]['kode'] }} &bull; {{ $top3[2]['student']->kelas ?? 'XII RPL 1' }}</div>
                <div class="fs-4 fw-bold text-dark">{{ number_format($top3[2]['nilai_akhir'], 2) }}</div>
            </div>
        </div>
        @endif
    </div>

    <!-- TABEL UTAMA: HASIL PERANGKINGAN -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold"><i class="bi bi-list-ol text-primary me-2"></i>Daftar Peringkat Lengkap Siswa</h6>
            <span class="badge bg-light text-muted border">Urut Nilai Tertinggi</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom table-hover align-middle mb-0 text-center">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Peringkat</th>
                            <th class="text-start">Kode / Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Nilai Akhir</th>
                            <th class="text-end pe-4">Status Rekomendasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($calculation['ranked_results'] as $res)
                        <tr>
                            <td>
                                @if($res['ranking'] == 1)
                                    <span class="badge bg-warning text-dark fs-6 px-3 py-1 rounded-pill fw-bold">
                                        <i class="bi bi-trophy-fill me-1"></i> Juara 1
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- GRAFIK RANKING (BERSIH) -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Grafik Perbandingan Nilai Siswa</h6>
        </div>
        <div class="card-body p-4">
            <canvas id="rankingChart" height="200"></canvas>
        </div>
    </div>

    <!-- RINCIAN PERHITUNGAN TEKNIS (AKORDION MINIMALIS) -->
    <div class="accordion mb-4" id="accordionDetail">
        <div class="accordion-item border rounded-3 overflow-hidden shadow-xs">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button collapsed fw-bold py-3 bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDetail">
                    <i class="bi bi-calculator me-2 text-primary"></i> Klik di sini jika ingin melihat rincian tabel perhitungan (Utility & Bobot)
                </button>
            </h2>
            <div id="collapseDetail" class="accordion-collapse collapse" data-bs-parent="#accordionDetail">
                <div class="accordion-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle mb-0 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start ps-3">Nama Siswa</th>
                                    @foreach($criteria as $c)
                                    <th>{{ $c->nama }} ({{ $c->kode }})</th>
                                    @endforeach
                                    <th class="table-primary text-primary">Nilai Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($calculation['ranked_results'] as $res)
                                <tr>
                                    <td class="text-start ps-3 fw-bold">{{ $res['nama'] }}</td>
                                    @foreach($criteria as $c)
                                    <td>
                                        <div>Utility: <strong>{{ number_format($res['utility_scores'][$c->id], 1) }}</strong></div>
                                        <small class="text-muted">(Skor: {{ $res['raw_scores'][$c->id] }})</small>
                                    </td>
                                    @endforeach
                                    <td class="table-primary fw-bold text-primary">{{ number_format($res['nilai_akhir'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const ctx = document.getElementById('rankingChart').getContext('2d');
    const chartLabels = {!! json_encode($chartLabels) !!};
    const chartScores = {!! json_encode($chartScores) !!};

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Nilai Akhir SMART',
                data: chartScores,
                backgroundColor: 'rgba(79, 70, 229, 0.85)',
                borderColor: '#4f46e5',
                borderWidth: 1.5,
                borderRadius: 8,
                barPercentage: 0.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: { color: 'rgba(0,0,0,0.04)' }
                },
                x: {
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endsection
