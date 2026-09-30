@extends('layouts.app')

@section('title', 'Data Alternatif Siswa')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-people-fill text-primary me-2"></i>Data Alternatif Siswa (Kandidat Berprestasi)</h4>
            <p class="text-muted mb-0 small">Kelola data siswa (alternatif) yang dinilai dalam sistem. Siswa A1 s.d A5 merupakan sampel acuan dari Jurnal SMK Mandiri.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary btn-sm px-3 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                <i class="bi bi-person-plus-fill me-1"></i> Input Alternatif Baru
            </button>
            <a href="{{ route('assessments.index') }}" class="btn btn-warning btn-sm px-3 rounded-pill text-dark fw-semibold shadow-sm">
                <i class="bi bi-pencil-square me-1"></i> Input Nilai Siswa
            </a>
        </div>
    </div>

    <!-- Table of Students -->
    <div class="card mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h6 class="mb-0 fw-bold"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Daftar Alternatif Siswa Terdaftar</h6>
            <span class="badge bg-primary-subtle text-primary">Total: {{ $students->count() }} Alternatif</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width: 80px;">Kode</th>
                            <th>NIS</th>
                            <th>Nama Lengkap Siswa</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th class="text-center">L/P</th>
                            <th class="text-center">Status Penilaian</th>
                            <th class="text-end pe-3" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $s)
                        <tr>
                            <td class="ps-3">
                                <span class="badge bg-primary fw-bold fs-6 px-2 py-1">{{ $s->kode }}</span>
                            </td>
                            <td>
                                <code>{{ $s->nis ?? '-' }}</code>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $s->nama }}</div>
                                @if(in_array($s->kode, ['A1', 'A2', 'A3', 'A4', 'A5']))
                                    <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 0.7rem;">Sampel Jurnal</span>
                                @endif
                            </td>
                            <td>{{ $s->kelas }}</td>
                            <td>{{ $s->jurusan }}</td>
                            <td class="text-center">
                                <span class="badge {{ $s->jenis_kelamin == 'L' ? 'bg-primary-subtle text-primary' : 'bg-pink-subtle text-danger' }} rounded-circle p-2">
                                    {{ $s->jenis_kelamin }}
                                </span>
                            </td>
                            <td class="text-center">
                                @php
                                    $assessCount = $s->assessments->count();
                                @endphp
                                @if($assessCount >= 5)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-check-circle-fill me-1"></i> Lengkap ({{ $assessCount }} Kriteria)
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle">
                                        <i class="bi bi-clock-history me-1"></i> Belum Lengkap ({{ $assessCount }}/5)
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#editStudentModal{{ $s->id }}" title="Edit Siswa">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteStudentModal{{ $s->id }}" title="Hapus Siswa">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Edit Student -->
                        <div class="modal fade" id="editStudentModal{{ $s->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('students.update', $s->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Siswa {{ $s->kode }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row mb-3">
                                                <div class="col-4">
                                                    <label class="form-label small fw-bold">Kode Alternatif</label>
                                                    <input type="text" class="form-control" value="{{ $s->kode }}" disabled>
                                                </div>
                                                <div class="col-8">
                                                    <label class="form-label small fw-bold">NIS</label>
                                                    <input type="text" name="nis" class="form-control" value="{{ $s->nis }}">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Nama Lengkap</label>
                                                <input type="text" name="nama" class="form-control" value="{{ $s->nama }}" required>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <label class="form-label small fw-bold">Kelas</label>
                                                    <input type="text" name="kelas" class="form-control" value="{{ $s->kelas }}" required>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small fw-bold">Jenis Kelamin</label>
                                                    <select name="jenis_kelamin" class="form-select">
                                                        <option value="L" {{ $s->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-Laki</option>
                                                        <option value="P" {{ $s->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Jurusan</label>
                                                <input type="text" name="jurusan" class="form-control" value="{{ $s->jurusan }}" required>
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

                        <!-- Modal Delete Student -->
                        <div class="modal fade" id="deleteStudentModal{{ $s->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content">
                                    <form action="{{ route('students.destroy', $s->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-body text-center p-4">
                                            <i class="bi bi-person-x text-danger fs-1"></i>
                                            <h6 class="fw-bold mt-2">Hapus Alternatif?</h6>
                                            <p class="small text-muted mb-3">Siswa {{ $s->kode }} - {{ $s->nama }} beserta seluruh penilaiannya akan dihapus.</p>
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                Belum ada alternatif siswa. Klik tombol "Input Alternatif Baru" untuk menambahkan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Student (Input Alternatif Baru) -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('students.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus text-primary me-2"></i>Input Alternatif Siswa Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2 small mb-3">
                        <i class="bi bi-info-circle me-1"></i> Alternatif yang ditambahkan akan otomatis diikutsertakan dalam matriks penilaian dan perangkingan SMART.
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label class="form-label small fw-bold">Kode Alternatif</label>
                            <input type="text" name="kode" class="form-control" value="{{ $nextKode }}" required placeholder="Contoh: A6">
                        </div>
                        <div class="col-8">
                            <label class="form-label small fw-bold">NIS (Nomor Induk Siswa)</label>
                            <input type="text" name="nis" class="form-control" placeholder="Contoh: 2024006">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap Siswa</label>
                        <input type="text" name="nama" class="form-control" placeholder="Masukkan nama siswa" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Kelas</label>
                            <input type="text" name="kelas" class="form-control" value="XII RPL 1" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="L" selected>Laki-Laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Jurusan</label>
                        <input type="text" name="jurusan" class="form-control" value="Rekayasa Perangkat Lunak" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Alternatif</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
