@extends('layouts.app')

@section('title', 'Input Penilaian Siswa')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-pencil-square text-primary me-2"></i>Input Penilaian Alternatif Siswa (Tabel 4 Jurnal)</h4>
            <p class="text-muted mb-0 small">Masukkan nilai skor parameter untuk masing-masing kriteria sesuai indikator penilaian (skala 20 – 100).</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-primary btn-sm px-3 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#singleInputModal">
                <i class="bi bi-plus-lg me-1"></i> Form Input Nilai Siswa
            </button>
            <form action="{{ route('assessments.reset-journal') }}" method="POST" class="d-inline" onsubmit="return confirm('Reset penilaian siswa ke data asli Tabel 4 Jurnal (A1 s.d A5)?');">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm px-3 rounded-pill shadow-sm">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset ke Data Jurnal (A1-A5)
                </button>
            </form>
            <a href="{{ route('smart.index') }}" class="btn btn-warning btn-sm px-3 rounded-pill text-dark fw-semibold shadow-sm">
                <i class="bi bi-calculator-fill me-1"></i> Hitung SMART & Ranking
            </a>
        </div>
    </div>

    <!-- Parameter Guide Quick Reference Card -->
    <div class="card mb-4 bg-light bg-opacity-60 border-primary-subtle">
        <div class="card-body p-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-info-circle-fill text-primary"></i>
                <strong class="small text-primary text-uppercase">Panduan Konversi Skor Parameter (Tabel 3 Jurnal):</strong>
            </div>
            <div class="row g-2 small text-muted">
                <div class="col-md">
                    <strong>K1 (Pengetahuan):</strong> &ge;80 (70), 75-79 (60), 70-74 (50), 60-69 (40), &lt;60 (30)
                </div>
                <div class="col-md">
                    <strong>K2 (Keterampilan):</strong> &ge;95 (100), 90-94 (90), 85-89 (80), 80-84 (70), dst
                </div>
                <div class="col-md">
                    <strong>K3 (Sikap):</strong> &ge;84 (100), 63-83 (80), 42-62 (60), 21-41 (40), &le;20 (20)
                </div>
                <div class="col-md">
                    <strong>K4 (Kehadiran):</strong> Alpa 0 (100), 1-3 (80), 4-6 (60), 7-10 (40), &gt;10 (20)
                </div>
                <div class="col-md">
                    <strong>K5 (Ekskul):</strong> Juara (100), Peserta (80), Aktif (60), Pasif (40), Tidak Ikut (20)
                </div>
            </div>
        </div>
    </div>

    <!-- Matriks Input Nilai (Batch Table Form) -->
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <div>
                <h6 class="mb-0 fw-bold"><i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i>Matriks Penilaian Alternatif ($C_{out}$)</h6>
                <small class="text-muted">Anda dapat mengubah langsung skor parameter di tabel ini lalu klik "Simpan Seluruh Penilaian".</small>
            </div>
            <span class="badge bg-primary-subtle text-primary">{{ $students->count() }} Alternatif &bull; {{ $criteria->count() }} Kriteria</span>
        </div>
        <div class="card-body p-0">
            <form action="{{ route('assessments.batch') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-custom table-hover align-middle mb-0 text-center">
                        <thead>
                            <tr>
                                <th class="ps-3 text-start" style="width: 80px;">Kode</th>
                                <th class="text-start">Nama Siswa</th>
                                @foreach($criteria as $c)
                                <th>
                                    <div>{{ $c->kode }}</div>
                                    <small class="text-muted fw-normal">{{ $c->nama }}</small>
                                </th>
                                @endforeach
                                <th class="text-end pe-3" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $s)
                            <tr>
                                <td class="ps-3 text-start">
                                    <span class="badge bg-primary fw-bold px-2 py-1">{{ $s->kode }}</span>
                                </td>
                                <td class="text-start">
                                    <div class="fw-bold">{{ $s->nama }}</div>
                                    <small class="text-muted">{{ $s->kelas }}</small>
                                </td>
                                @foreach($criteria as $c)
                                @php
                                    $val = $matrix[$s->id][$c->id]['skor'] ?? 20;
                                @endphp
                                <td style="width: 130px;">
                                    <input type="number" 
                                           name="matrix[{{ $s->id }}][{{ $c->id }}]" 
                                           class="form-control form-control-sm text-center fw-bold bg-light" 
                                           value="{{ $val }}" 
                                           min="20" 
                                           max="100" 
                                           step="5" 
                                           required>
                                </td>
                                @endforeach
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editStudentAssess{{ $s->id }}" title="Pilih dari Dropdown">
                                        <i class="bi bi-ui-checks"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ 3 + $criteria->count() }}" class="text-center py-4 text-muted">
                                    Belum ada data siswa. Tambahkan siswa terlebih dahulu pada menu "Data Alternatif Siswa".
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($students->count() > 0)
                <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                    <span class="small text-muted">
                        <i class="bi bi-shield-check text-success me-1"></i> Seluruh skor divalidasi antara rentang 20 s/d 100.
                    </span>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm rounded-pill">
                        <i class="bi bi-save2-fill me-1"></i> Simpan Seluruh Penilaian
                    </button>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>

<!-- Modal Single Student Input with Dropdowns -->
<div class="modal fade" id="singleInputModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('assessments.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-check text-primary me-2"></i>Input Penilaian Siswa Berdasarkan Parameter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Pilih Alternatif Siswa</label>
                        <select name="student_id" class="form-select form-select-lg" required>
                            <option value="">-- Pilih Siswa --</option>
                            @foreach($students as $st)
                            <option value="{{ $st->id }}">{{ $st->kode }} - {{ $st->nama }} ({{ $st->kelas }})</option>
                            @endforeach
                        </select>
                    </div>

                    <h6 class="fw-bold text-primary mb-3">Pilih Skor Parameter Kriteria:</h6>
                    <div class="row g-3">
                        @foreach($criteria as $crit)
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <label class="form-label fw-bold small text-dark d-flex justify-content-between">
                                    <span>{{ $crit->kode }} - {{ $crit->nama }}</span>
                                    <span class="badge bg-secondary-subtle text-dark">Benefit</span>
                                </label>
                                @if($crit->parameters->count() > 0)
                                <select name="scores[{{ $crit->id }}]" class="form-select form-select-sm" required>
                                    @foreach($crit->parameters as $p)
                                    <option value="{{ $p->skor }}">
                                        {{ $p->label }} &rarr; Skor: {{ $p->skor }}
                                    </option>
                                    @endforeach
                                </select>
                                @else
                                <input type="number" name="scores[{{ $crit->id }}]" class="form-control form-control-sm" placeholder="20 - 100" min="20" max="100" value="60" required>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Nilai Siswa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Individual Edit for each student -->
@foreach($students as $st)
<div class="modal fade" id="editStudentAssess{{ $st->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('assessments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="student_id" value="{{ $st->id }}">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Penilaian Detail: {{ $st->kode }} - {{ $st->nama }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        @foreach($criteria as $crit)
                        @php
                            $currentScore = $matrix[$st->id][$crit->id]['skor'] ?? 20;
                        @endphp
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 bg-light">
                                <label class="form-label fw-bold small text-dark d-flex justify-content-between">
                                    <span>{{ $crit->kode }} - {{ $crit->nama }}</span>
                                    <span class="badge bg-primary-subtle text-primary">Skor saat ini: {{ $currentScore }}</span>
                                </label>
                                @if($crit->parameters->count() > 0)
                                <select name="scores[{{ $crit->id }}]" class="form-select form-select-sm" required>
                                    @foreach($crit->parameters as $p)
                                    <option value="{{ $p->skor }}" {{ $p->skor == $currentScore ? 'selected' : '' }}>
                                        {{ $p->label }} &rarr; Skor: {{ $p->skor }}
                                    </option>
                                    @endforeach
                                </select>
                                @else
                                <input type="number" name="scores[{{ $crit->id }}]" class="form-control form-control-sm" min="20" max="100" value="{{ $currentScore }}" required>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
