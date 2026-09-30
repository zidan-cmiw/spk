<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\AHPService;
use App\Services\SMARTService;

class SpkAhpSmartTest extends TestCase
{
    /**
     * Test AHP calculation with consistent preset
     */
    public function test_ahp_consistent_calculation(): void
    {
        $ahpService = new AHPService();
        $critIds = [1, 2, 3, 4, 5];
        $preset = $ahpService->getPresetJournalConsistentMatrix();

        $result = $ahpService->calculate($critIds, $preset);

        $this->assertTrue($result['success']);
        $this->assertLessThan(0.1, $result['cr']);
        $this->assertTrue($result['is_consistent']);
        $this->assertEquals(1.0, round(array_sum($result['weights']), 2));
    }

    /**
     * Test SMART utility benefit calculation according to journal
     */
    public function test_smart_utility_benefit(): void
    {
        $smartService = new SMARTService();

        // Testing Journal sample: Cmin = 20, Cmax = 100
        // Score 60 -> (60-20)/80 * 100 = 50.0
        $this->assertEquals(50.0, $smartService->calculateUtility(60, 20, 100, 'benefit'));

        // Score 70 -> (70-20)/80 * 100 = 62.5
        $this->assertEquals(62.5, $smartService->calculateUtility(70, 20, 100, 'benefit'));

        // Score 80 -> (80-20)/80 * 100 = 75.0
        $this->assertEquals(75.0, $smartService->calculateUtility(80, 20, 100, 'benefit'));

        // Score 100 -> (100-20)/80 * 100 = 100.0
        $this->assertEquals(100.0, $smartService->calculateUtility(100, 20, 100, 'benefit'));

        // Score 20 -> (20-20)/80 * 100 = 0.0
        $this->assertEquals(0.0, $smartService->calculateUtility(20, 20, 100, 'benefit'));
    }

    /**
     * Test full ranking replication of Journal Table 6 & 7
     */
    public function test_replication_of_journal_table_6_and_7(): void
    {
        $smartService = new SMARTService();

        $students = [
            (object)['id' => 1, 'kode' => 'A1', 'nama' => 'Ahmad Fauzi'],
            (object)['id' => 2, 'kode' => 'A2', 'nama' => 'Bella Safitri'],
            (object)['id' => 3, 'kode' => 'A3', 'nama' => 'Candra Wijaya'],
            (object)['id' => 4, 'kode' => 'A4', 'nama' => 'Dedi Kurniawan'],
            (object)['id' => 5, 'kode' => 'A5', 'nama' => 'Eka Rahmawati'],
        ];

        $criteria = [
            (object)['id' => 1, 'kode' => 'K1', 'tipe' => 'benefit'],
            (object)['id' => 2, 'kode' => 'K2', 'tipe' => 'benefit'],
            (object)['id' => 3, 'kode' => 'K3', 'tipe' => 'benefit'],
            (object)['id' => 4, 'kode' => 'K4', 'tipe' => 'benefit'],
            (object)['id' => 5, 'kode' => 'K5', 'tipe' => 'benefit'],
        ];

        $journalWeights = [
            1 => 0.40,
            2 => 0.20,
            3 => 0.15,
            4 => 0.15,
            5 => 0.10,
        ];

        $assessments = [
            1 => [1 => 60, 2 => 70, 3 => 60, 4 => 100, 5 => 20],
            2 => [1 => 60, 2 => 70, 3 => 80, 4 => 100, 5 => 20],
            3 => [1 => 60, 2 => 70, 3 => 80, 4 => 60,  5 => 60],
            4 => [1 => 70, 2 => 70, 3 => 100,4 => 100, 5 => 60],
            5 => [1 => 60, 2 => 70, 3 => 80, 4 => 80,  5 => 40],
        ];

        $result = $smartService->calculateRanking($students, $criteria, $journalWeights, $assessments);

        $scores = [];
        foreach ($result['ranked_results'] as $r) {
            $scores[$r['kode']] = $r['nilai_akhir'];
        }

        // Must exactly match Table 6 in the journal
        $this->assertEquals(72.5, $scores['A4']);
        $this->assertEquals(58.75, $scores['A2']);
        $this->assertEquals(57.5, $scores['A5']);
        $this->assertEquals(56.25, $scores['A3']);
        $this->assertEquals(55.0, $scores['A1']);

        // Rank order: A4, A2, A5, A3, A1
        $rankOrder = array_map(fn($r) => $r['kode'], $result['ranked_results']);
        $this->assertEquals(['A4', 'A2', 'A5', 'A3', 'A1'], $rankOrder);
    }
}
