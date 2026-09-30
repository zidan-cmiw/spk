<?php

namespace App\Http\Controllers;

use App\Models\Criterion;
use App\Models\Student;
use App\Models\Period;
use App\Models\AhpSession;
use App\Models\Assessment;
use App\Services\SMARTService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function print(Request $request, SMARTService $smartService)
    {
        $period = Period::where('is_active', true)->first() ?? Period::first();
        $criteria = Criterion::orderBy('urutan')->get();
        $students = Student::orderBy('kode')->get();

        $activeSession = AhpSession::where('is_active', true)->with('weights.criterion')->first();
        $mode = $request->query('mode', 'ahp');

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

        $rawAssessments = Assessment::where('period_id', $period->id ?? 0)->get();
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

        return view('reports.print', compact(
            'period',
            'criteria',
            'students',
            'activeSession',
            'mode',
            'weights',
            'calculation'
        ));
    }
}
