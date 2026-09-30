@extends('layouts.app')

@section('title', 'Input Siswa (Alternatif)')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary fs-6 px-3 py-1 rounded-pill">Langkah 1</span>
                <h4 class="fw-bold mb-0">Input Siswa (Alternatif)</h4>
            </div>
            <p class="text-muted small mt-1 mb-0">Tambahkan siswa yang akan dinilai di sini. Setelah menambah siswa, lanjutkan ke Langkah 2 (Input Nilai).</p>
        </div>
        <a href="{{ route('assessments.index') }}" class="btn btn-warning text-dark fw-bold btn-sm px-4 py-2 rounded-pill shadow-sm">
            Lanjut ke Langkah 2: Input Nilai <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <!-- FORM INPUT LANGSUNG DI HALAMAN (JELAS & MUDAH DIPAHAMI) -->
    <div class="card input-card-hero mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
            <div class="bg-primary text-white rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                <i class="bi bi-plus-lg fs-6"></i>
            </div>
            <h6 class="fw-bold mb-0 text-dark">Form Input Siswa Baru</h6>
            <span class="text-muted small ms-auto">Ketik data siswa baru pada form ini:</span>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('students.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-dark">Kode Alternatif</label>
                        <input type="text" name="kode" class="form-control form-control-lg fw-bold text-primary bg-light" value="{{ $nextKode }}" required placeholder="A6">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-dark">NIS (Nomor Induk Siswa)</label>
                        <input type="text" name="nis" class="form-control form-control-lg" placeholder="Contoh: 2024006">
                    </div>
                    <div class="col-md-7">
                        <label class="form-label small fw-bold text-dark">Nama Lengkap Siswa</label>
                        <input type="text" name="nama" class="form-control form-control-lg" placeholder="Contoh: Muhammad Farhan" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark">Kelas</label>
                        <input type="text" name="kelas" class="form-control" value="XII RPL 1" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-bold text-dark">Jurusan</label>
                        <input type="text" name="jurusan" class="form-control" value="Rekayasa Perangkat Lunak" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-dark">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select">
                            <option value="L" selected>Laki-Laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <div class="col-12 text-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-person-plus-fill me-2"></i> + Simpan & Tambah Siswa
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- TABEL DAFTAR SISWA -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold"><i class="bi bi-people-fill text-primary me-2"></i>Daftar Siswa Terdaftar ({{ $students->count() }} Siswa)</h6>
            <small class="text-muted">A1 s.d A5 adalah data siswa dari jurnal acuan.</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Kode</th>
                            <th style="width: 120px;">NIS</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th class="text-center" style="width: 80px;">L/P</th>
                            <th class="text-center" style="width: 170px;">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $s)
                        <tr>
                            <td>
                                <span class="badge bg-primary fs-6 px-2 py-1 fw-bold">{{ $s->kode }}</span>
                            </td>
                            <td><code>{{ $s->nis ?? '-' }}</code></td>
                            <td>
                                <div class="fw-bold text-dark">{{ $s->nama }}</div>
                                @if(in_array($s->kode, ['A1', 'A2', 'A3', 'A4', 'A5']))
                                    <span class="badge bg-secondary-subtle text-muted" style="font-size: 0.68rem;">Data Jurnal</span>
                                @endif
                            </td>
                            <td>{{ $s->kelas }}</td>
                            <td>{{ $s->jurusan }}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">{{ $s->jenis_kelamin }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('assessments.index') }}" class="btn btn-sm btn-warning text-dark fw-bold px-3 py-1 rounded-pill me-1" title="Isi Nilai">
                                    <i class="bi bi-pencil-fill me-1"></i> Isi Nilai
                                </a>
                                <button class="btn btn-sm btn-light border text-danger" data-bs-toggle="modal" data-bs-target="#delModal{{ $s->id }}" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Delete -->
                        <div class="modal fade" id="delModal{{ $s->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                <div class="modal-content">
                                    <form action="{{ route('students.destroy', $s->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-body text-center p-4">
                                            <i class="bi bi-exclamation-circle text-danger fs-1"></i>
                                            <h6 class="fw-bold mt-2">Hapus Siswa Ini?</h6>
                                            <p class="small text-muted mb-3">{{ $s->kode }} - {{ $s->nama }}</p>
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-sm btn-danger px-3">Hapus</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada siswa terdaftar. Silakan ketik nama siswa pada form di atas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
