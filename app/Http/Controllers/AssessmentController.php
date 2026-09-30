<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Criterion;
use App\Models\Period;
use App\Models\Assessment;
use App\Models\Parameter;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index()
    {
        $period = Period::where('is_active', true)->first() ?? Period::first();
        $criteria = Criterion::with('parameters')->orderBy('urutan')->get();
        $students = Student::orderBy('kode')->get();

        // Load existing assessments for this period
        $assessmentsRaw = Assessment::where('period_id', $period->id ?? 0)->get();
        $matrix = [];
        foreach ($assessmentsRaw as $a) {
            $matrix[$a->student_id][$a->criteria_id] = [
                'nilai_asli' => $a->nilai_asli,
                'skor' => $a->skor_parameter,
            ];
        }

        return view('assessments.index', compact('period', 'criteria', 'students', 'matrix'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:20|max:100',
        ]);

        $period = Period::where('is_active', true)->first();
        if (!$period) {
            return redirect()->back()->with('error', 'Tidak ada periode aktif.');
        }

        $studentId = $request->student_id;
        foreach ($request->scores as $critId => $skor) {
            Assessment::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'criteria_id' => $critId,
                    'period_id' => $period->id,
                ],
                [
                    'nilai_asli' => (string)$skor,
                    'skor_parameter' => floatval($skor),
                ]
            );
        }

        $student = Student::find($studentId);
        return redirect()->route('assessments.index')->with('success', 'Nilai parameter untuk siswa ' . ($student->nama ?? '') . ' berhasil disimpan.');
    }

    public function updateBatch(Request $request)
    {
        $request->validate([
            'matrix' => 'required|array',
        ]);

        $period = Period::where('is_active', true)->first();
        if (!$period) {
            return redirect()->back()->with('error', 'Tidak ada periode aktif.');
        }

        foreach ($request->matrix as $studentId => $criteriaScores) {
            foreach ($criteriaScores as $critId => $score) {
                Assessment::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'criteria_id' => $critId,
                        'period_id' => $period->id,
                    ],
                    [
                        'nilai_asli' => (string)$score,
                        'skor_parameter' => floatval($score),
                    ]
                );
            }
        }

        return redirect()->route('assessments.index')->with('success', 'Seluruh matriks penilaian siswa berhasil disimpan.');
    }

    /**
     * Reset to the exact 5 students and scores from Tabel 4 in the journal
     */
    public function resetToJournal()
    {
        $period = Period::where('is_active', true)->first();
        if (!$period) {
            return redirect()->back()->with('error', 'Tidak ada periode aktif.');
        }

        $criteria = Criterion::all()->keyBy('kode');
        $journalScores = [
            'A1' => ['K1' => 60, 'K2' => 70, 'K3' => 60, 'K4' => 100, 'K5' => 20],
            'A2' => ['K1' => 60, 'K2' => 70, 'K3' => 80, 'K4' => 100, 'K5' => 20],
            'A3' => ['K1' => 60, 'K2' => 70, 'K3' => 80, 'K4' => 60,  'K5' => 60],
            'A4' => ['K1' => 70, 'K2' => 70, 'K3' => 100,'K4' => 100, 'K5' => 60],
            'A5' => ['K1' => 60, 'K2' => 70, 'K3' => 80, 'K4' => 80,  'K5' => 40],
        ];

        foreach ($journalScores as $kode => $scores) {
            $student = Student::where('kode', $kode)->first();
            if ($student) {
                foreach ($scores as $critKode => $val) {
                    if (isset($criteria[$critKode])) {
                        Assessment::updateOrCreate(
                            [
                                'student_id' => $student->id,
                                'criteria_id' => $criteria[$critKode]->id,
                                'period_id' => $period->id,
                            ],
                            [
                                'nilai_asli' => (string)$val,
                                'skor_parameter' => $val,
                            ]
                        );
                    }
                }
            }
        }

        return redirect()->route('assessments.index')->with('success', 'Data penilaian berhasil di-reset sesuai Tabel 4 Jurnal SMK Mandiri (A1 - A5).');
    }
}
