@extends('layouts.app')

@section('title', 'Kriteria & Parameter')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-sliders text-primary me-2"></i>Master Kriteria & Parameter Penilaian</h4>
            <p class="text-muted mb-0 small">Kriteria baku penilaian prestasi belajar siswa berdasarkan acuan Jurnal SMK Mandiri (Herdiana dkk., 2024).</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary btn-sm px-3 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#addCriterionModal">
                <i class="bi bi-plus-circle me-1"></i> Tambah Kriteria
            </button>
        </div>
    </div>

    <!-- Tabel Kriteria -->
    <div class="card mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h6 class="mb-0 fw-bold"><i class="bi bi-table me-2 text-primary"></i>Daftar Kriteria Penilaian (Tabel 1 Jurnal)</h6>
            <span class="badge bg-primary-subtle text-primary">Total: {{ $criteria->count() }} Kriteria</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width: 80px;">Kode</th>
                            <th>Nama Kriteria</th>
                            <th>Tipe Kriteria</th>
                            <th class="text-center">Bobot Acuan Jurnal</th>
                            <th>Deskripsi & Sumber Data</th>
                            <th class="text-center" style="width: 140px;">Parameter</th>
                            <th class="text-end pe-3" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($criteria as $c)
                        <tr>
                            <td class="ps-3">
                                <span class="badge bg-dark fw-bold px-2 py-1">{{ $c->kode }}</span>
                            </td>
                            <td>
                                <strong class="text-dark">{{ $c->nama }}</strong>
                            </td>
                            <td>
                                <span class="badge {{ $c->tipe == 'benefit' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger' }} text-capitalize">
                                    <i class="bi bi-arrow-up-right me-1"></i>{{ $c->tipe }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-dark fw-bold fs-6 px-3 py-1">
                                    {{ ($c->bobot_default * 100) }}% ({{ $c->bobot_default }})
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $c->deskripsi ?? '-' }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                    {{ $c->parameters->count() }} Rentang / Nilai
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#editCritModal{{ $c->id }}" title="Edit Kriteria">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteCritModal{{ $c->id }}" title="Hapus Kriteria">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Edit Criterion -->
                        <div class="modal fade" id="editCritModal{{ $c->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('criteria.update', $c->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Kriteria {{ $c->kode }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Nama Kriteria</label>
                                                <input type="text" name="nama" class="form-control" value="{{ $c->nama }}" required>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <label class="form-label small fw-bold">Tipe Kriteria</label>
                                                    <select name="tipe" class="form-select">
                                                        <option value="benefit" {{ $c->tipe == 'benefit' ? 'selected' : '' }}>Benefit</option>
                                                        <option value="cost" {{ $c->tipe == 'cost' ? 'selected' : '' }}>Cost</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small fw-bold">Bobot Default (0-1)</label>
                                                    <input type="number" step="0.01" min="0" max="1" name="bobot_default" class="form-control" value="{{ $c->bobot_default }}" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Deskripsi</label>
                                                <textarea name="deskripsi" class="form-control" rows="2">{{ $c->deskripsi }}</textarea>
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

                        <!-- Modal Delete Criterion -->
                        <div class="modal fade" id="deleteCritModal{{ $c->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content">
                                    <form action="{{ route('criteria.destroy', $c->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-body text-center p-4">
                                            <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                                            <h6 class="fw-bold mt-2">Hapus Kriteria?</h6>
                                            <p class="small text-muted mb-3">Kriteria {{ $c->kode }} - {{ $c->nama }} beserta seluruh parameter dan nilainya akan dihapus.</p>
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sub-Kriteria / Parameter Kriteria (Tabel 3 Jurnal) -->
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <div>
                <h6 class="mb-0 fw-bold"><i class="bi bi-list-stars me-2 text-primary"></i>Tabel Parameter Kriteria (Tabel 3 di Jurnal)</h6>
                <small class="text-muted">Konversi data kualitatif dan rentang nilai rapor menjadi skor baku skala 20 – 100.</small>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                @foreach($criteria as $crit)
                <div class="col-lg-6">
                    <div class="border rounded-3 p-3 bg-white h-100 shadow-xs">
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom mb-3">
                            <h6 class="fw-bold mb-0 text-dark">
                                <span class="badge bg-primary me-1">{{ $crit->kode }}</span> {{ $crit->nama }}
                            </h6>
                            <button class="btn btn-xs btn-outline-primary py-1 px-2 rounded" data-bs-toggle="modal" data-bs-target="#addParamModal{{ $crit->id }}">
                                <i class="bi bi-plus"></i> Tambah Parameter
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr class="table-light">
                                        <th>Indikator Penilaian / Rentang Nilai</th>
                                        <th class="text-center" style="width: 90px;">Skor</th>
                                        <th class="text-end" style="width: 50px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($crit->parameters as $param)
                                    <tr>
                                        <td>
                                            <span class="fw-medium">{{ $param->label }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-dark-subtle text-dark fw-bold px-2 py-1">
                                                {{ $param->skor }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <form action="{{ route('criteria.parameter.destroy', $param->id) }}" method="POST" onsubmit="return confirm('Hapus parameter ini?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted small py-2">Belum ada parameter.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Modal Add Parameter -->
                <div class="modal fade" id="addParamModal{{ $crit->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('criteria.parameter.store', $crit->id) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Tambah Parameter untuk {{ $crit->kode }} ({{ $crit->nama }})</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Label / Indikator Penilaian</label>
                                        <input type="text" name="label" class="form-control" placeholder="Contoh: Nilai 75 - 79 atau Mengikuti Kejuaraan" required>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <label class="form-label small fw-bold">Skor Parameter (20 - 100)</label>
                                            <input type="number" step="1" min="0" max="100" name="skor" class="form-control" placeholder="60" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="form-label small fw-bold">Batas Min (Opsional)</label>
                                            <input type="number" step="0.01" name="batas_min" class="form-control" placeholder="75">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small fw-bold">Batas Max (Opsional)</label>
                                            <input type="number" step="0.01" name="batas_max" class="form-control" placeholder="79.99">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan Parameter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Criterion -->
<div class="modal fade" id="addCriterionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('criteria.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Kriteria Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-4">
                            <label class="form-label small fw-bold">Kode Kriteria</label>
                            <input type="text" name="kode" class="form-control" placeholder="K6" required>
                        </div>
                        <div class="col-8">
                            <label class="form-label small fw-bold">Nama Kriteria</label>
                            <input type="text" name="nama" class="form-control" placeholder="Contoh: Kedisiplinan" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Tipe Kriteria</label>
                            <select name="tipe" class="form-select">
                                <option value="benefit" selected>Benefit (Makin tinggi makin baik)</option>
                                <option value="cost">Cost (Makin rendah makin baik)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Bobot Default (0-1)</label>
                            <input type="number" step="0.01" min="0" max="1" name="bobot_default" class="form-control" value="0.10" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="2" placeholder="Keterangan sumber data"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kriteria</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
