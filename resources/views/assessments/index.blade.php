@extends('layouts.app')

@section('title', 'Input Nilai Siswa')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark fs-6 px-3 py-1 rounded-pill fw-bold">Langkah 2</span>
                <h4 class="fw-bold mb-0">Input Nilai Siswa per Kriteria</h4>
            </div>
            <p class="text-muted small mt-1 mb-0">Pilih siswa di bawah ini lalu tentukan nilainya untuk masing-masing kriteria.</p>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('assessments.reset-journal') }}" method="POST" onsubmit="return confirm('Reset nilai ke data asli jurnal (A1 s.d A5)?');">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm px-3 rounded-pill">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset ke Data Jurnal
                </button>
            </form>
            <a href="{{ route('smart.index') }}" class="btn btn-primary btn-sm px-4 rounded-pill fw-bold shadow-sm">
                Lanjut ke Langkah 4: Lihat Ranking <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>

    <!-- FORM INPUT NILAI LANGSUNG DI HALAMAN (SANGAT JELAS & PRAKTIS) -->
    <div class="card input-card-hero mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
            <div class="bg-warning text-dark rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                <i class="bi bi-pencil-fill fs-6"></i>
            </div>
            <h6 class="fw-bold mb-0 text-dark">Form Pengisian Nilai Siswa</h6>
            <span class="text-muted small ms-auto">Pilih siswa lalu pilih nilai kriterianya:</span>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('assessments.store') }}" method="POST">
                @csrf
                <!-- Pilih Siswa -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark fs-6">1. Pilih Siswa yang Akan Dinilai:</label>
                    <select name="student_id" class="form-select form-select-lg fw-bold text-primary" required>
                        <option value="">-- Klik di sini untuk memilih nama siswa --</option>
                        @foreach($students as $st)
                        <option value="{{ $st->id }}">{{ $st->kode }} - {{ $st->nama }} ({{ $st->kelas }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- 5 Kotak Input Nilai Kriteria -->
                <label class="form-label fw-bold text-dark fs-6 mb-2">2. Tentukan Nilai untuk 5 Kriteria:</label>
                <div class="row g-3">
                    @foreach($criteria as $crit)
                    <div class="col-md-6 col-lg">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <div class="fw-bold text-dark mb-1">
                                <span class="badge bg-primary me-1">{{ $crit->kode }}</span>
                                {{ $crit->nama }}
                            </div>
                            <small class="text-muted d-block mb-2">{{ $crit->deskripsi }}</small>

                            @if($crit->parameters->count() > 0)
                            <select name="scores[{{ $crit->id }}]" class="form-select form-select-sm fw-semibold" required>
                                @foreach($crit->parameters as $p)
                                <option value="{{ $p->skor }}">
                                    {{ $p->label }} (Skor: {{ $p->skor }})
                                </option>
                                @endforeach
                            </select>
                            @else
                            <input type="number" name="scores[{{ $crit->id }}]" class="form-control form-control-sm" min="20" max="100" value="60" required>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-warning btn-lg px-5 text-dark fw-bold rounded-pill shadow-sm">
                        <i class="bi bi-save-fill me-2"></i> 💾 Simpan Nilai Siswa Ini
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TABEL REKAPITULASI NILAI SELURUH SISWA -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold"><i class="bi bi-table text-primary me-2"></i>Tabel Rekap Nilai Siswa (Skor Parameter 20 – 100)</h6>
            <small class="text-muted">Anda juga bisa mengubah angka langsung di tabel ini lalu klik simpan.</small>
        </div>
        <div class="card-body p-0">
            <form action="{{ route('assessments.batch') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-custom table-hover align-middle mb-0 text-center">
                        <thead>
                            <tr>
                                <th class="text-start" style="width: 80px;">Kode</th>
                                <th class="text-start">Nama Siswa</th>
                                @foreach($criteria as $c)
                                <th>
                                    <div>{{ $c->kode }}</div>
                                    <small class="text-muted fw-normal">{{ $c->nama }}</small>
                                </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $s)
                            <tr>
                                <td class="text-start">
                                    <span class="badge bg-primary fw-bold px-2 py-1">{{ $s->kode }}</span>
                                </td>
                                <td class="text-start fw-bold text-dark">{{ $s->nama }}</td>
                                @foreach($criteria as $c)
                                @php
                                    $val = $matrix[$s->id][$c->id]['skor'] ?? 20;
                                @endphp
                                <td style="width: 120px;">
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
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ 2 + $criteria->count() }}" class="text-center py-4 text-muted">
                                    Belum ada data siswa. Tambahkan siswa terlebih dahulu pada menu "1. Input Siswa".
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($students->count() > 0)
                <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                    <span class="small text-muted">
                        <i class="bi bi-info-circle me-1"></i> Skor parameter standar jurnal adalah rentang 20 s/d 100.
                    </span>
                    <button type="submit" class="btn btn-outline-primary btn-sm px-4 rounded-pill fw-bold">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan di Tabel
                    </button>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
