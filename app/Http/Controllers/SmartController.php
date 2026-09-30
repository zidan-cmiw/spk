<?php

namespace App\Http\Controllers;

use App\Models\Criterion;
use App\Models\Student;
use App\Models\Period;
use App\Models\AhpSession;
use App\Models\Assessment;
use App\Models\CalculationResult;
use App\Services\SMARTService;
use Illuminate\Http\Request;

class SmartController extends Controller
{
    public function index(Request $request, SMARTService $smartService)
    {
        $period = Period::where('is_active', true)->first() ?? Period::first();
        $criteria = Criterion::orderBy('urutan')->get();
        $students = Student::orderBy('kode')->get();

        $activeSession = AhpSession::where('is_active', true)
            ->with('weights.criterion')
            ->first();

        // Selected weight mode: 'ahp' or 'jurnal' (default: 'ahp')
        $mode = $request->query('mode', 'ahp');

        // Fetch weights based on mode
        $ahpWeights = [];
        if ($activeSession) {
            foreach ($activeSession->weights as $w) {
                $ahpWeights[$w->criteria_id] = $w->bobot;
            }
        } else {
            // fallback equal weights
            foreach ($criteria as $c) {
                $ahpWeights[$c->id] = 1.0 / count($criteria);
            }
        }

        $journalWeights = [];
        foreach ($criteria as $c) {
            $journalWeights[$c->id] = $c->bobot_default ?? 0.20;
        }

        $selectedWeights = ($mode === 'jurnal') ? $journalWeights : $ahpWeights;

        // Group assessments
        $rawAssessments = Assessment::where('period_id', $period->id ?? 0)->get();
        $groupedAssessments = [];
        foreach ($rawAssessments as $a) {
            $groupedAssessments[$a->student_id][$a->criteria_id] = $a->skor_parameter;
        }

        // Calculate SMART for chosen mode
        $calculation = $smartService->calculateRanking(
            $students->all(),
            $criteria->all(),
            $selectedWeights,
            $groupedAssessments
        );

        // Also calculate for the other mode to provide Sensitivity Comparison Table
        $ahpCalculation = $smartService->calculateRanking(
            $students->all(),
            $criteria->all(),
            $ahpWeights,
            $groupedAssessments
        );

        $journalCalculation = $smartService->calculateRanking(
            $students->all(),
            $criteria->all(),
            $journalWeights,
            $groupedAssessments
        );

        // Build side-by-side comparison map
        $comparison = [];
        $journalRankMap = [];
        foreach ($journalCalculation['ranked_results'] as $item) {
            $journalRankMap[$item['student_id']] = [
                'nilai' => $item['nilai_akhir'],
                'ranking' => $item['ranking'],
            ];
        }

        foreach ($ahpCalculation['ranked_results'] as $item) {
            $sId = $item['student_id'];
            $jRank = $journalRankMap[$sId]['ranking'] ?? 0;
            $jScore = $journalRankMap[$sId]['nilai'] ?? 0.0;
            $rankDiff = $jRank - $item['ranking']; // positive means improved rank in AHP

            $comparison[] = [
                'kode' => $item['kode'],
                'nama' => $item['nama'],
                'journal_score' => $jScore,
                'journal_rank' => $jRank,
                'ahp_score' => $item['nilai_akhir'],
                'ahp_rank' => $item['ranking'],
                'rank_diff' => $rankDiff,
            ];
        }

        // Prepare chart data
        $chartLabels = array_map(fn($r) => $r['kode'] . ' - ' . $r['nama'], $calculation['ranked_results']);
        $chartScores = array_map(fn($r) => $r['nilai_akhir'], $calculation['ranked_results']);

        return view('smart.index', compact(
            'period',
            'criteria',
            'students',
            'activeSession',
            'mode',
            'selectedWeights',
            'ahpWeights',
            'journalWeights',
            'calculation',
            'comparison',
            'chartLabels',
            'chartScores'
        ));
    }

    public function saveResults(Request $request, SMARTService $smartService)
    {
        $period = Period::where('is_active', true)->first();
        if (!$period) {
            return redirect()->back()->with('error', 'Tidak ada periode aktif.');
        }

        $activeSession = AhpSession::where('is_active', true)->with('weights')->first();
        $criteria = Criterion::orderBy('urutan')->get();
        $students = Student::orderBy('kode')->get();

        $mode = $request->input('mode', 'ahp');

        $weights = [];
        if ($mode === 'jurnal') {
            foreach ($criteria as $c) {
                $weights[$c->id] = $c->bobot_default ?? 0.20;
            }
        } else {
            if ($activeSession) {
                foreach ($activeSession->weights as $w) {
                    $weights[$w->criteria_id] = $w->bobot;
                }
            } else {
                foreach ($criteria as $c) {
                    $weights[$c->id] = 1.0 / count($criteria);
                }
            }
        }

        $rawAssessments = Assessment::where('period_id', $period->id)->get();
        $groupedAssessments = [];
        foreach ($rawAssessments as $a) {
            $groupedAssessments[$a->student_id][$a->criteria_id] = $a->skor_parameter;
        }

        $calculation = $smartService->calculateRanking(
            $students->all(),
            $criteria->all(),
            $weights,
            $groupedAssessments
        );

        // Delete existing calculation results for this period & mode
        CalculationResult::where('period_id', $period->id)
            ->where('mode_bobot', $mode)
            ->delete();

        foreach ($calculation['ranked_results'] as $item) {
            CalculationResult::create([
                'period_id' => $period->id,
                'session_id' => ($mode === 'ahp' && $activeSession) ? $activeSession->id : null,
                'student_id' => $item['student_id'],
                'mode_bobot' => $mode,
                'utility_scores' => $item['utility_scores'],
                'nilai_akhir' => $item['nilai_akhir'],
                'ranking' => $item['ranking'],
            ]);
        }

        return redirect()->route('smart.index', ['mode' => $mode])->with('success', 'Hasil perankingan berhasil disimpan secara permanen ke database.');
    }
}
