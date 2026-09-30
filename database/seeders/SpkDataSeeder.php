<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Period;
use App\Models\Criterion;
use App\Models\Parameter;
use App\Models\Student;
use App\Models\Assessment;
use App\Models\AhpSession;
use App\Models\AhpComparison;
use App\Models\CriteriaWeight;
use App\Services\AHPService;
use App\Services\SMARTService;

class SpkDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Period
        $period = Period::create([
            'nama' => 'Tahun Ajaran 2024/2025',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        // 2. Criteria (K1 - K5)
        $criteriaData = [
            [
                'kode' => 'K1',
                'nama' => 'Pengetahuan',
                'tipe' => 'benefit',
                'bobot_default' => 0.40,
                'urutan' => 1,
                'deskripsi' => 'Nilai akademik rata-rata pengetahuan dari rapor siswa.',
            ],
            [
                'kode' => 'K2',
                'nama' => 'Keterampilan',
                'tipe' => 'benefit',
                'bobot_default' => 0.20,
                'urutan' => 2,
                'deskripsi' => 'Nilai praktik/keterampilan kejuruan dari rapor siswa.',
            ],
            [
                'kode' => 'K3',
                'nama' => 'Sikap',
                'tipe' => 'benefit',
                'bobot_default' => 0.15,
                'urutan' => 3,
                'deskripsi' => 'Evaluasi catatan perilaku dan budi pekerti siswa.',
            ],
            [
                'kode' => 'K4',
                'nama' => 'Kehadiran (Absensi)',
                'tipe' => 'benefit',
                'bobot_default' => 0.15,
                'urutan' => 4,
                'deskripsi' => 'Tingkat kedisiplinan dan jumlah hari tanpa keterangan (alpa).',
            ],
            [
                'kode' => 'K5',
                'nama' => 'Ekstrakurikuler',
                'tipe' => 'benefit',
                'bobot_default' => 0.10,
                'urutan' => 5,
                'deskripsi' => 'Keaktifan dan prestasi dalam kegiatan ekstrakurikuler.',
            ],
        ];

        $criteriaMap = [];
        foreach ($criteriaData as $c) {
            $created = Criterion::create($c);
            $criteriaMap[$c['kode']] = $created;
        }

        // 3. Parameters (Tabel 3 di Jurnal + perbaikan rentang >= 80 untuk Pengetahuan)
        // K1: Pengetahuan
        $k1Params = [
            ['label' => 'Nilai ≥ 80', 'batas_min' => 80, 'batas_max' => 100, 'skor' => 70],
            ['label' => 'Nilai 75 – 79', 'batas_min' => 75, 'batas_max' => 79.99, 'skor' => 60],
            ['label' => 'Nilai 70 – 74', 'batas_min' => 70, 'batas_max' => 74.99, 'skor' => 50],
            ['label' => 'Nilai 60 – 69', 'batas_min' => 60, 'batas_max' => 69.99, 'skor' => 40],
            ['label' => 'Nilai < 60', 'batas_min' => 0, 'batas_max' => 59.99, 'skor' => 30],
        ];
        foreach ($k1Params as $p) {
            $p['criteria_id'] = $criteriaMap['K1']->id;
            Parameter::create($p);
        }

        // K2: Keterampilan
        $k2Params = [
            ['label' => 'Nilai ≥ 95', 'batas_min' => 95, 'batas_max' => 100, 'skor' => 100],
            ['label' => 'Nilai 90 – 94', 'batas_min' => 90, 'batas_max' => 94.99, 'skor' => 90],
            ['label' => 'Nilai 85 – 89', 'batas_min' => 85, 'batas_max' => 89.99, 'skor' => 80],
            ['label' => 'Nilai 80 – 84', 'batas_min' => 80, 'batas_max' => 84.99, 'skor' => 70],
            ['label' => 'Nilai 75 – 79', 'batas_min' => 75, 'batas_max' => 79.99, 'skor' => 60],
            ['label' => 'Nilai 70 – 74', 'batas_min' => 70, 'batas_max' => 74.99, 'skor' => 50],
            ['label' => 'Nilai 60 – 69', 'batas_min' => 60, 'batas_max' => 69.99, 'skor' => 40],
            ['label' => 'Nilai < 60', 'batas_min' => 0, 'batas_max' => 59.99, 'skor' => 30],
        ];
        foreach ($k2Params as $p) {
            $p['criteria_id'] = $criteriaMap['K2']->id;
            Parameter::create($p);
        }

        // K3: Sikap
        $k3Params = [
            ['label' => 'Sikap Sangat Baik (≥ 84)', 'batas_min' => 84, 'batas_max' => 100, 'skor' => 100],
            ['label' => 'Sikap Baik (63 – 83)', 'batas_min' => 63, 'batas_max' => 83.99, 'skor' => 80],
            ['label' => 'Sikap Cukup (42 – 62)', 'batas_min' => 42, 'batas_max' => 62.99, 'skor' => 60],
            ['label' => 'Sikap Kurang (21 – 41)', 'batas_min' => 21, 'batas_max' => 41.99, 'skor' => 40],
            ['label' => 'Sikap Sangat Kurang (0 – 20)', 'batas_min' => 0, 'batas_max' => 20.99, 'skor' => 20],
        ];
        foreach ($k3Params as $p) {
            $p['criteria_id'] = $criteriaMap['K3']->id;
            Parameter::create($p);
        }

        // K4: Kehadiran
        $k4Params = [
            ['label' => 'Tanpa Keterangan = 0 hari', 'batas_min' => 0, 'batas_max' => 0, 'skor' => 100],
            ['label' => 'Tanpa Keterangan 1 – 3 hari', 'batas_min' => 1, 'batas_max' => 3, 'skor' => 80],
            ['label' => 'Tanpa Keterangan 4 – 6 hari', 'batas_min' => 4, 'batas_max' => 6, 'skor' => 60],
            ['label' => 'Tanpa Keterangan 7 – 10 hari', 'batas_min' => 7, 'batas_max' => 10, 'skor' => 40],
            ['label' => 'Tanpa Keterangan > 10 hari', 'batas_min' => 11, 'batas_max' => 100, 'skor' => 20],
        ];
        foreach ($k4Params as $p) {
            $p['criteria_id'] = $criteriaMap['K4']->id;
            Parameter::create($p);
        }

        // K5: Ekstrakurikuler
        $k5Params = [
            ['label' => 'Mengikuti Kejuaraan dan Mendapat Penghargaan', 'batas_min' => null, 'batas_max' => null, 'skor' => 100],
            ['label' => 'Mengikuti Kejuaraan Tapi Tidak Mendapat Penghargaan', 'batas_min' => null, 'batas_max' => null, 'skor' => 80],
            ['label' => 'Mengikuti Ekskul dan Aktif', 'batas_min' => null, 'batas_max' => null, 'skor' => 60],
            ['label' => 'Mengikuti Ekskul tapi tidak aktif', 'batas_min' => null, 'batas_max' => null, 'skor' => 40],
            ['label' => 'Tidak Mengikuti Ekskul', 'batas_min' => null, 'batas_max' => null, 'skor' => 20],
        ];
        foreach ($k5Params as $p) {
            $p['criteria_id'] = $criteriaMap['K5']->id;
            Parameter::create($p);
        }

        // 4. Students (Data Alternatif Jurnal A1 - A5)
        $studentsData = [
            ['kode' => 'A1', 'nis' => '2024001', 'nama' => 'Ahmad Fauzi', 'kelas' => 'XII RPL 1', 'jurusan' => 'Rekayasa Perangkat Lunak', 'jenis_kelamin' => 'L'],
            ['kode' => 'A2', 'nis' => '2024002', 'nama' => 'Bella Safitri', 'kelas' => 'XII RPL 1', 'jurusan' => 'Rekayasa Perangkat Lunak', 'jenis_kelamin' => 'P'],
            ['kode' => 'A3', 'nis' => '2024003', 'nama' => 'Candra Wijaya', 'kelas' => 'XII RPL 1', 'jurusan' => 'Rekayasa Perangkat Lunak', 'jenis_kelamin' => 'L'],
            ['kode' => 'A4', 'nis' => '2024004', 'nama' => 'Dedi Kurniawan', 'kelas' => 'XII RPL 1', 'jurusan' => 'Rekayasa Perangkat Lunak', 'jenis_kelamin' => 'L'],
            ['kode' => 'A5', 'nis' => '2024005', 'nama' => 'Eka Rahmawati', 'kelas' => 'XII RPL 1', 'jurusan' => 'Rekayasa Perangkat Lunak', 'jenis_kelamin' => 'P'],
        ];

        $studentMap = [];
        foreach ($studentsData as $s) {
            $created = Student::create($s);
            $studentMap[$s['kode']] = $created;
        }

        // 5. Assessments Data (Sesuai Tabel 4 di Jurnal)
        // A1: K1=60, K2=70, K3=60, K4=100, K5=20
        // A2: K1=60, K2=70, K3=80, K4=100, K5=20
        // A3: K1=60, K2=70, K3=80, K4=60,  K5=60
        // A4: K1=70, K2=70, K3=100,K4=100, K5=60
        // A5: K1=60, K2=70, K3=80, K4=80,  K5=40
        $journalScores = [
            'A1' => ['K1' => 60, 'K2' => 70, 'K3' => 60, 'K4' => 100, 'K5' => 20],
            'A2' => ['K1' => 60, 'K2' => 70, 'K3' => 80, 'K4' => 100, 'K5' => 20],
            'A3' => ['K1' => 60, 'K2' => 70, 'K3' => 80, 'K4' => 60,  'K5' => 60],
            'A4' => ['K1' => 70, 'K2' => 70, 'K3' => 100,'K4' => 100, 'K5' => 60],
            'A5' => ['K1' => 60, 'K2' => 70, 'K3' => 80, 'K4' => 80,  'K5' => 40],
        ];

        $assessmentDataGroup = [];
        foreach ($journalScores as $kode => $scores) {
            $student = $studentMap[$kode];
            foreach ($scores as $critKode => $skorVal) {
                $crit = $criteriaMap[$critKode];
                Assessment::create([
                    'student_id' => $student->id,
                    'criteria_id' => $crit->id,
                    'period_id' => $period->id,
                    'nilai_asli' => (string)$skorVal,
                    'skor_parameter' => $skorVal,
                ]);
                $assessmentDataGroup[$student->id][$crit->id] = $skorVal;
            }
        }

        // 6. Setup Initial AHP Session with Consistent Pairwise Comparisons
        $ahpService = new AHPService();
        $critList = [$criteriaMap['K1'], $criteriaMap['K2'], $criteriaMap['K3'], $criteriaMap['K4'], $criteriaMap['K5']];
        $critIds = array_map(fn($c) => $c->id, $critList);

        $pairwisePreset = $ahpService->getPresetJournalConsistentMatrix();
        $ahpResult = $ahpService->calculate($critIds, $pairwisePreset);

        $ahpSession = AhpSession::create([
            'period_id' => $period->id,
            'nama_sesi' => 'Model Integrasi AHP - Penilaian Siswa Berprestasi SMK Mandiri',
            'deskripsi' => 'Sesi pembobotan AHP konsisten (CR < 0.1) untuk mengatasi kelemahan bobot manual jurnal.',
            'lambda_max' => $ahpResult['lambda_max'],
            'ci' => $ahpResult['ci'],
            'cr' => $ahpResult['cr'],
            'is_consistent' => $ahpResult['is_consistent'],
            'is_active' => true,
        ]);

        // Save comparisons
        for ($i = 0; $i < count($critIds); $i++) {
            for ($j = 0; $j < count($critIds); $j++) {
                AhpComparison::create([
                    'session_id' => $ahpSession->id,
                    'criteria_a_id' => $critIds[$i],
                    'criteria_b_id' => $critIds[$j],
                    'nilai' => $pairwisePreset[$i][$j],
                ]);
            }
        }

        // Save weights
        foreach ($critIds as $cId) {
            CriteriaWeight::create([
                'session_id' => $ahpSession->id,
                'criteria_id' => $cId,
                'bobot' => $ahpResult['criteria_weights'][$cId],
            ]);
        }
    }
}
