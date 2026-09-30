<?php

namespace App\Services;

class SMARTService
{
    /**
     * Min and Max parameter limits from SMK Mandiri journal
     */
    protected float $cMin = 20.0;
    protected float $cMax = 100.0;

    /**
     * Calculate utility score for a single criterion value
     * Formula benefit (SMART): u_i(a_i) = (C_out - C_min) / (C_max - C_min) * 100
     */
    public function calculateUtility(float $cOut, float $cMin = 20.0, float $cMax = 100.0, string $type = 'benefit'): float
    {
        if ($cMax == $cMin) {
            return 100.0;
        }

        if ($type === 'cost') {
            $utility = (($cMax - $cOut) / ($cMax - $cMin)) * 100.0;
        } else {
            // Benefit (standar penilaian prestasi siswa di jurnal)
            $utility = (($cOut - $cMin) / ($cMax - $cMin)) * 100.0;
        }

        // Clamp between 0 and 100
        return max(0.0, min(100.0, $utility));
    }

    /**
     * Calculate full SMART ranking
     *
     * @param array $students List of student models / objects
     * @param array $criteria List of criteria models / objects
     * @param array $weights Normalized weights keyed by criterion ID: [crit_id => weight]
     * @param array $assessments Grouped assessments: [student_id => [crit_id => score]]
     * @return array Calculation breakdown and ranked results
     */
    public function calculateRanking(array $students, array $criteria, array $weights, array $assessments): array
    {
        $rawScores = [];
        $utilityScores = [];
        $weightedComponents = [];
        $finalScores = [];

        // Ensure weights sum to 1.0 (or normalize if not)
        $weightSum = array_sum($weights);
        $normalizedWeights = [];
        foreach ($criteria as $crit) {
            $w = $weights[$crit->id] ?? 0.0;
            $normalizedWeights[$crit->id] = $weightSum > 0 ? ($w / $weightSum) : 0.0;
        }

        foreach ($students as $student) {
            $sId = $student->id;
            $rawScores[$sId] = [];
            $utilityScores[$sId] = [];
            $weightedComponents[$sId] = [];
            $totalScore = 0.0;

            foreach ($criteria as $crit) {
                $cId = $crit->id;
                // Get parameter score (defaults to cMin if empty)
                $score = floatval($assessments[$sId][$cId] ?? $this->cMin);
                $rawScores[$sId][$cId] = $score;

                // Utility
                $u = $this->calculateUtility($score, $this->cMin, $this->cMax, $crit->tipe ?? 'benefit');
                $utilityScores[$sId][$cId] = $u;

                // Weighted utility: w_j * u_j
                $w = $normalizedWeights[$cId] ?? 0.0;
                $comp = $w * $u;
                $weightedComponents[$sId][$cId] = $comp;

                $totalScore += $comp;
            }

            $finalScores[] = [
                'student' => $student,
                'student_id' => $sId,
                'kode' => $student->kode,
                'nama' => $student->nama,
                'raw_scores' => $rawScores[$sId],
                'utility_scores' => $utilityScores[$sId],
                'weighted_components' => $weightedComponents[$sId],
                'nilai_akhir' => round($totalScore, 4),
            ];
        }

        // Sort descending by final score
        usort($finalScores, function ($a, $b) {
            if ($b['nilai_akhir'] == $a['nilai_akhir']) {
                return 0;
            }
            return ($b['nilai_akhir'] > $a['nilai_akhir']) ? 1 : -1;
        });

        // Assign rankings and recommendation status
        $rankedResults = [];
        $rank = 1;
        foreach ($finalScores as $item) {
            $item['ranking'] = $rank;

            if ($rank === 1) {
                $item['rekomendasi'] = 'Sangat Direkomendasikan (Juara 1)';
                $item['badge_class'] = 'bg-success text-white';
            } elseif ($rank === 2) {
                $item['rekomendasi'] = 'Sangat Direkomendasikan (Juara 2)';
                $item['badge_class'] = 'bg-primary text-white';
            } elseif ($rank === 3) {
                $item['rekomendasi'] = 'Direkomendasikan (Juara 3)';
                $item['badge_class'] = 'bg-info text-white';
            } elseif ($rank <= 5) {
                $item['rekomendasi'] = 'Direkomendasikan';
                $item['badge_class'] = 'bg-warning text-dark';
            } else {
                $item['rekomendasi'] = 'Cukup';
                $item['badge_class'] = 'bg-secondary text-white';
            }

            $rankedResults[] = $item;
            $rank++;
        }

        return [
            'normalized_weights' => $normalizedWeights,
            'raw_scores' => $rawScores,
            'utility_scores' => $utilityScores,
            'ranked_results' => $rankedResults,
            'c_min' => $this->cMin,
            'c_max' => $this->cMax,
        ];
    }
}
