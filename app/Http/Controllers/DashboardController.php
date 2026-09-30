<?php

namespace App\Http\Controllers;

use App\Models\Criterion;
use App\Models\Student;
use App\Models\Period;
use App\Models\AhpSession;
use App\Models\Assessment;
use App\Services\SMARTService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(SMARTService $smartService)
    {
        $criteria = Criterion::orderBy('urutan')->get();
        $students = Student::orderBy('kode')->get();
        $activePeriod = Period::where('is_active', true)->first() ?? Period::first();
        $activeAhp = AhpSession::where('is_active', true)->with('weights.criterion')->first();

        // Count stats
        $totalCriteria = $criteria->count();
        $totalStudents = $students->count();
        $totalAssessments = Assessment::where('period_id', $activePeriod->id ?? 0)->count();

        // Calculate quick preview ranking with active AHP weights
        $rankedPreview = [];
        if ($totalStudents > 0 && $totalCriteria > 0 && $activeAhp) {
            $weights = [];
            foreach ($activeAhp->weights as $w) {
                $weights[$w->criteria_id] = $w->bobot;
            }

            // Group assessments
            $rawAssessments = Assessment::where('period_id', $activePeriod->id ?? 0)->get();
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
            $rankedPreview = array_slice($calculation['ranked_results'], 0, 5);
        }

        return view('dashboard', compact(
            'totalCriteria',
            'totalStudents',
            'totalAssessments',
            'activePeriod',
            'activeAhp',
            'rankedPreview',
            'criteria',
            'students'
        ));
    }
}
