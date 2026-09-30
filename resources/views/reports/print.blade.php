<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekomendasi Siswa Berprestasi - SMK Mandiri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #fff;
            padding: 20px;
        }

        .report-container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
        }

        .header-logo {
            width: 85px;
            height: 85px;
        }

        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }

        .table-report th, .table-report td {
            border: 1px solid #000 !important;
            padding: 6px 8px;
            font-size: 11pt;
        }

        .table-report th {
            background-color: #f2f2f2 !important;
            font-weight: bold;
            text-align: center;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
            .report-container {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="report-container">
        <!-- Action Buttons (Hidden on Print) -->
        <div class="no-print mb-4 d-flex justify-content-between align-items-center bg-light p-3 border rounded">
            <div>
                <strong>Laporan Siap Cetak:</strong> Rekomendasi Siswa Berprestasi (Metode {{ $mode == 'ahp' ? 'Integrasi AHP + SMART' : 'SMART (Bobot Manual Jurnal)' }})
            </div>
            <div>
                <button onclick="window.print()" class="btn btn-primary btn-sm px-4 fw-bold">
                    <i class="bi bi-printer"></i> Cetak / Simpan PDF
                </button>
                <button onclick="window.close()" class="btn btn-secondary btn-sm px-3">
                    Tutup
                </button>
            </div>
        </div>

        <!-- Kop Surat SMK Mandiri -->
        <div class="kop-surat text-center position-relative">
            <div class="row align-items-center">
                <div class="col-2 text-center">
                    <img src="https://img.icons8.com/color/96/school.png" alt="Logo" class="header-logo">
                </div>
                <div class="col-10 text-center">
                    <h5 class="mb-0 fw-bold" style="letter-spacing: 1px;">YAYASAN PENDIDIKAN MANDIRI</h5>
                    <h3 class="mb-0 fw-bold" style="letter-spacing: 1.5px;">SMK MANDIRI BANDUNG</h3>
                    <div style="font-size: 10pt;" class="mt-1">
                        Terakreditasi "A" &bull; NSS: 322026001001 &bull; NPSN: 20219800<br>
                        Kompetensi Keahlian: Rekayasa Perangkat Lunak, Teknik Komputer dan Jaringan, Multimedia<br>
                        Jl. Cikutra No. 113, Kota Bandung, Jawa Barat 40124 &bull; Telp: (022) 7208888 &bull; Email: info@smkmandiri.sch.id
                    </div>
                </div>
            </div>
        </div>

        <!-- Judul Laporan -->
        <div class="text-center mb-4">
            <h5 class="fw-bold text-uppercase text-decoration-underline mb-1">
                SURAT KEPUTUSAN PENETAPAN SISWA BERPRESTASI
            </h5>
            <div>Nomor: 421.5/082/SMK-MND/SPK/{{ date('Y') }}</div>
            <div class="mt-1" style="font-size: 11pt;">
                Periode: <strong>{{ $period->nama }} &mdash; Semester {{ $period->semester }}</strong>
            </div>
        </div>

        <!-- Pengantar -->
        <p style="text-align: justify; line-height: 1.5; font-size: 11pt;">
            Berdasarkan hasil evaluasi dan penilaian akhir peserta didik melalui <strong>Sistem Pendukung Keputusan (SPK)</strong> menggunakan 
            metode <strong>{{ $mode == 'ahp' ? 'Integrasi Analytic Hierarchy Process (AHP) dan Simple Multi Attribute Rating Technique (SMART)' : 'SMART dengan Bobot Manual Jurnal Acuan' }}</strong> 
            terhadap kriteria Pengetahuan, Keterampilan, Sikap, Kehadiran, serta Ekstrakurikuler, maka diputuskan rekomendasi siswa berprestasi sebagai berikut:
        </p>

        <!-- Tabel Hasil Keputusan -->
        <table class="table table-report table-sm w-100 mb-4 align-middle">
            <thead>
                <tr>
                    <th style="width: 50px;">Rank</th>
                    <th style="width: 60px;">Kode</th>
                    <th style="width: 100px;">NIS</th>
                    <th class="text-start">Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Pengetahuan (K1)</th>
                    <th>Keterampilan (K2)</th>
                    <th>Sikap (K3)</th>
                    <th>Kehadiran (K4)</th>
                    <th>Ekskul (K5)</th>
                    <th>Nilai Akhir</th>
                    <th>Keterangan / Rekomendasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($calculation['ranked_results'] as $item)
                <tr>
                    <td class="text-center fw-bold">{{ $item['ranking'] }}</td>
                    <td class="text-center fw-bold">{{ $item['kode'] }}</td>
                    <td class="text-center">{{ $item['student']->nis ?? '-' }}</td>
                    <td><strong>{{ $item['nama'] }}</strong></td>
                    <td class="text-center">{{ $item['student']->kelas ?? 'XII RPL 1' }}</td>
                    <td class="text-center">{{ $item['raw_scores'][$criteria->where('kode', 'K1')->first()->id] ?? '-' }}</td>
                    <td class="text-center">{{ $item['raw_scores'][$criteria->where('kode', 'K2')->first()->id] ?? '-' }}</td>
                    <td class="text-center">{{ $item['raw_scores'][$criteria->where('kode', 'K3')->first()->id] ?? '-' }}</td>
                    <td class="text-center">{{ $item['raw_scores'][$criteria->where('kode', 'K4')->first()->id] ?? '-' }}</td>
                    <td class="text-center">{{ $item['raw_scores'][$criteria->where('kode', 'K5')->first()->id] ?? '-' }}</td>
                    <td class="text-center fw-bold">{{ number_format($item['nilai_akhir'], 2) }}</td>
                    <td class="text-center">
                        @if($item['ranking'] == 1)
                            <strong>Juara 1 (Teladan)</strong>
                        @elseif($item['ranking'] == 2)
                            <strong>Juara 2</strong>
                        @elseif($item['ranking'] == 3)
                            <strong>Juara 3</strong>
                        @else
                            Direkomendasikan
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Catatan Metodologi -->
        <div class="p-2 mb-4 border" style="font-size: 9.5pt; background-color: #fafafa;">
            <strong>Catatan Metodologi SPK:</strong><br>
            @if($mode == 'ahp')
                1. Pembobotan kriteria dihitung menggunakan metode <strong>AHP Saaty (1-9)</strong> dengan rasio konsistensi <strong>CR = {{ $activeSession->cr ?? '0.0074' }} &lt; 0.1 (KONSISTEN)</strong>.<br>
                2. Penilaian utility alternatif dihitung dengan metode <strong>SMART</strong> benefit: <code>u = (Cout - 20) / (100 - 20) &times; 100</code>.<br>
                3. Keputusan rekomendasi sah secara matematis dan dapat dipertanggungjawabkan secara transparan.
            @else
                1. Menggunakan pembobotan manual jurnal acuan: K1=40%, K2=20%, K3=15%, K4=15%, K5=10%.<br>
                2. Utility dihitung dengan SMART benefit.
            @endif
        </div>

        <!-- Tanda Tangan -->
        <div class="row mt-5" style="font-size: 11pt;">
            <div class="col-6 text-center">
                <div>Mengetahui,</div>
                <div>Ketua Panitia Seleksi</div>
                <div style="height: 75px;"></div>
                <div class="fw-bold text-decoration-underline">Drs. H. Mulyadi, M.Pd.</div>
                <div>NIP. 19750812 200212 1 003</div>
            </div>
            <div class="col-6 text-center">
                <div>Bandung, {{ date('d F Y') }}</div>
                <div>Kepala Sekolah SMK Mandiri</div>
                <div style="height: 75px;"></div>
                <div class="fw-bold text-decoration-underline">Dr. Ir. H. Ahmad Sanusi, M.M.</div>
                <div>NIP. 19680315 199303 1 002</div>
            </div>
        </div>
    </div>

</body>
</html>
