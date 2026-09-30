<?php

namespace App\Http\Controllers;

use App\Models\Criterion;
use App\Models\Period;
use App\Models\AhpSession;
use App\Models\AhpComparison;
use App\Models\CriteriaWeight;
use App\Services\AHPService;
use Illuminate\Http\Request;

class AhpController extends Controller
{
    public function index(AHPService $ahpService)
    {
        $period = Period::where('is_active', true)->first() ?? Period::first();
        $criteria = Criterion::orderBy('urutan')->get();
        $critIds = $criteria->pluck('id')->all();

        $activeSession = AhpSession::where('is_active', true)
            ->with(['weights.criterion', 'comparisons'])
            ->first();

        // If no active session, look for latest session
        if (!$activeSession) {
            $activeSession = AhpSession::latest()->first();
        }

        // Build comparison matrix from active session or preset
        $matrix = [];
        $n = count($criteria);

        if ($activeSession && $activeSession->comparisons->count() > 0) {
            $compLookup = [];
            foreach ($activeSession->comparisons as $c) {
                $compLookup[$c->criteria_a_id][$c->criteria_b_id] = $c->nilai;
            }

            for ($i = 0; $i < $n; $i++) {
                for ($j = 0; $j < $n; $j++) {
                    $idA = $critIds[$i];
                    $idB = $critIds[$j];
                    if ($i === $j) {
                        $matrix[$i][$j] = 1.0;
                    } elseif (isset($compLookup[$idA][$idB])) {
                        $matrix[$i][$j] = floatval($compLookup[$idA][$idB]);
                    } elseif (isset($compLookup[$idB][$idA]) && $compLookup[$idB][$idA] > 0) {
                        $matrix[$i][$j] = 1.0 / floatval($compLookup[$idB][$idA]);
                    } else {
                        $matrix[$i][$j] = 1.0;
                    }
                }
            }
        } else {
            // Use default preset
            $matrix = $ahpService->getPresetJournalConsistentMatrix();
        }

        // Run calculation
        $ahpResult = $ahpService->calculate($critIds, $matrix);

        // Previous sessions list
        $sessions = AhpSession::with('weights.criterion')->latest()->take(5)->get();

        return view('ahp.index', compact(
            'period',
            'criteria',
            'matrix',
            'ahpResult',
            'activeSession',
            'sessions'
        ));
    }

    public function calculate(Request $request, AHPService $ahpService)
    {
        $period = Period::where('is_active', true)->first();
        if (!$period) {
            return redirect()->back()->with('error', 'Tidak ada periode aktif.');
        }

        $criteria = Criterion::orderBy('urutan')->get();
        $critIds = $criteria->pluck('id')->all();
        $n = count($criteria);

        // Pairwise input comes as comparisons[idA_idB]
        // or matrix[i][j]
        $matrix = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($i === $j) {
                    $matrix[$i][$j] = 1.0;
                } elseif ($i < $j) {
                    $key = $critIds[$i] . '_' . $critIds[$j];
                    $val = floatval($request->input("pair.{$key}", 1.0));
                    $matrix[$i][$j] = $val > 0 ? $val : 1.0;
                } else {
                    // reciprocal
                    $matrix[$i][$j] = ($matrix[$j][$i] > 0) ? (1.0 / $matrix[$j][$i]) : 1.0;
                }
            }
        }

        $ahpResult = $ahpService->calculate($critIds, $matrix);

        // If user requested to save
        $sessionName = $request->input('nama_sesi', 'Sesi AHP ' . date('d M Y H:i'));

        // Deactivate previous active sessions
        AhpSession::where('is_active', true)->update(['is_active' => false]);

        $session = AhpSession::create([
            'period_id' => $period->id,
            'nama_sesi' => $sessionName,
            'deskripsi' => 'Perhitungan matriks perbandingan berpasangan AHP',
            'lambda_max' => $ahpResult['lambda_max'],
            'ci' => $ahpResult['ci'],
            'cr' => $ahpResult['cr'],
            'is_consistent' => $ahpResult['is_consistent'],
            'is_active' => true,
        ]);

        // Save pairwise values
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                AhpComparison::create([
                    'session_id' => $session->id,
                    'criteria_a_id' => $critIds[$i],
                    'criteria_b_id' => $critIds[$j],
                    'nilai' => $matrix[$i][$j],
                ]);
            }
        }

        // Save weights
        foreach ($critIds as $cId) {
            CriteriaWeight::create([
                'session_id' => $session->id,
                'criteria_id' => $cId,
                'bobot' => $ahpResult['criteria_weights'][$cId] ?? 0.0,
            ]);
        }

        $statusMsg = $ahpResult['is_consistent']
            ? "Perhitungan AHP berhasil disimpan! Bobot kriteria konsisten (CR = {$ahpResult['cr']} < 0.1) dan kini aktif untuk metode SMART."
            : "Perhatian: Matriks perbandingan berpasangan TIDAK konsisten (CR = {$ahpResult['cr']} >= 0.1). Disarankan melakukan revisi nilai perbandingan.";

        return redirect()->route('ahp.index')->with($ahpResult['is_consistent'] ? 'success' : 'warning', $statusMsg);
    }

    public function loadPreset(AHPService $ahpService)
    {
        $period = Period::where('is_active', true)->first();
        if (!$period) {
            return redirect()->back()->with('error', 'Tidak ada periode aktif.');
        }

        $criteria = Criterion::orderBy('urutan')->get();
        $critIds = $criteria->pluck('id')->all();
        $preset = $ahpService->getPresetJournalConsistentMatrix();

        $ahpResult = $ahpService->calculate($critIds, $preset);

        // Deactivate old
        AhpSession::where('is_active', true)->update(['is_active' => false]);

        $session = AhpSession::create([
            'period_id' => $period->id,
            'nama_sesi' => 'Preset Konsisten AHP (CR = 0.0074 < 0.1)',
            'deskripsi' => 'Preset matriks AHP berpasangan skala Saaty yang selaras dengan hierarki jurnal SMK Mandiri.',
            'lambda_max' => $ahpResult['lambda_max'],
            'ci' => $ahpResult['ci'],
            'cr' => $ahpResult['cr'],
            'is_consistent' => true,
            'is_active' => true,
        ]);

        $n = count($critIds);
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                AhpComparison::create([
                    'session_id' => $session->id,
                    'criteria_a_id' => $critIds[$i],
                    'criteria_b_id' => $critIds[$j],
                    'nilai' => $preset[$i][$j],
                ]);
            }
        }

        foreach ($critIds as $cId) {
            CriteriaWeight::create([
                'session_id' => $session->id,
                'criteria_id' => $cId,
                'bobot' => $ahpResult['criteria_weights'][$cId] ?? 0.0,
            ]);
        }

        return redirect()->route('ahp.index')->with('success', 'Preset matriks AHP konsisten berhasil dimuat (CR = 0.0074 < 0.1).');
    }

    public function activate(AhpSession $session)
    {
        AhpSession::where('is_active', true)->update(['is_active' => false]);
        $session->update(['is_active' => true]);

        return redirect()->route('ahp.index')->with('success', 'Sesi AHP "' . $session->nama_sesi . '" telah diaktifkan untuk perhitungan SMART.');
    }
}
