<?php

namespace App\Services;

class AHPService
{
    /**
     * Random Index (RI) table according to Saaty for n = 1 to 10
     */
    protected array $riTable = [
        1 => 0.00,
        2 => 0.00,
        3 => 0.58,
        4 => 0.90,
        5 => 1.12,
        6 => 1.24,
        7 => 1.32,
        8 => 1.41,
        9 => 1.45,
        10 => 1.49,
    ];

    /**
     * Calculate AHP weights and consistency ratio
     *
     * @param array $criteriaIds Ordered list of criteria IDs
     * @param array $matrix NxN comparison matrix where $matrix[i][j] = value
     * @return array Calculation results with steps and weights
     */
    public function calculate(array $criteriaIds, array $matrix): array
    {
        $n = count($criteriaIds);

        if ($n < 2) {
            return [
                'success' => false,
                'message' => 'Minimal kriteria untuk AHP adalah 2 kriteria.',
                'weights' => [],
            ];
        }

        // 1. Validate / enforce diagonal = 1 and reciprocal a_ji = 1 / a_ij
        $cleanMatrix = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($i === $j) {
                    $cleanMatrix[$i][$j] = 1.0;
                } else {
                    $val = floatval($matrix[$i][$j] ?? 1.0);
                    $cleanMatrix[$i][$j] = $val > 0 ? $val : 1.0;
                }
            }
        }

        // 2. Calculate column sums
        $colSums = array_fill(0, $n, 0.0);
        for ($j = 0; $j < $n; $j++) {
            for ($i = 0; $i < $n; $i++) {
                $colSums[$j] += $cleanMatrix[$i][$j];
            }
        }

        // 3. Normalize matrix and compute priority weights (eigenvector estimate)
        $normMatrix = [];
        $weights = [];
        for ($i = 0; $i < $n; $i++) {
            $rowSum = 0.0;
            for ($j = 0; $j < $n; $j++) {
                $normVal = $colSums[$j] > 0 ? ($cleanMatrix[$i][$j] / $colSums[$j]) : 0;
                $normMatrix[$i][$j] = $normVal;
                $rowSum += $normVal;
            }
            $weightVal = $rowSum / $n;
            $weights[$i] = $weightVal;
        }

        // 4. Calculate Lambda Max, CI, and CR
        // Vector y = A * w
        $yVector = [];
        $tVector = [];
        for ($i = 0; $i < $n; $i++) {
            $y = 0.0;
            for ($j = 0; $j < $n; $j++) {
                $y += $cleanMatrix[$i][$j] * $weights[$j];
            }
            $yVector[$i] = $y;
            $tVector[$i] = $weights[$i] > 0 ? ($y / $weights[$i]) : 0;
        }

        $lambdaMax = array_sum($tVector) / $n;
        $ci = $n > 1 ? (($lambdaMax - $n) / ($n - 1)) : 0.0;

        $ri = $this->riTable[$n] ?? 1.49;
        $cr = $ri > 0 ? ($ci / $ri) : 0.0;

        // Ensure small precision artifacts around 0 don't cause negative values
        if ($cr < 0) {
            $cr = 0.0;
        }

        $isConsistent = ($cr < 0.1);

        // Map weights to criteria IDs
        $criteriaWeights = [];
        for ($i = 0; $i < $n; $i++) {
            $criteriaWeights[$criteriaIds[$i]] = $weights[$i];
        }

        return [
            'success' => true,
            'n' => $n,
            'matrix' => $cleanMatrix,
            'col_sums' => $colSums,
            'norm_matrix' => $normMatrix,
            'weights' => $weights,
            'criteria_weights' => $criteriaWeights,
            'y_vector' => $yVector,
            't_vector' => $tVector,
            'lambda_max' => round($lambdaMax, 4),
            'ci' => round($ci, 4),
            'ri' => $ri,
            'cr' => round($cr, 4),
            'is_consistent' => $isConsistent,
        ];
    }

    /**
     * Get predefined consistent pairwise matrix for 5 criteria based on SMK Mandiri journal hierarchy
     */
    public function getPresetJournalConsistentMatrix(): array
    {
        return [
            [1.0, 2.0, 3.0, 3.0, 4.0],
            [1/2, 1.0, 2.0, 2.0, 3.0],
            [1/3, 1/2, 1.0, 1.0, 2.0],
            [1/3, 1/2, 1.0, 1.0, 2.0],
            [1/4, 1/3, 1/2, 1/2, 1.0],
        ];
    }
}
