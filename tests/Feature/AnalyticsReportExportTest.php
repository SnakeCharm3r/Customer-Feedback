<?php

namespace Tests\Feature;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class AnalyticsReportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_consolidated_export_contains_the_dashboard_sections_and_charts(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
            'is_first_user' => true,
        ]);

        $this->feedback('positive', 'opd', 'client_experience', 'CCBRT-EXPORT-001');
        $this->feedback('negative', 'opd', 'waiting_time', 'CCBRT-EXPORT-002');
        $this->feedback('neutral', 'opd', 'client_experience', 'CCBRT-EXPORT-003');
        $this->feedback('negative', 'theatre', 'waiting_time', 'CCBRT-EXPORT-004');
        $this->feedback('positive', 'ipd', 'client_satisfaction', 'CCBRT-EXPORT-005', 'moshi');

        $response = $this->actingAs($admin)->get(route('reports.analytics.export.excel', ['location' => 'hq']));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $path = tempnam(sys_get_temp_dir(), 'analytics-export-');
        file_put_contents($path, $response->getContent());

        try {
            $reader = IOFactory::createReader('Xlsx');
            $reader->setIncludeCharts(true);
            $workbook = $reader->load($path);

            $this->assertSame([
                'Overview', 'Collection Means', 'General Feedback', 'OPD Feedback', 'IPD Feedback',
                'OTD Feedback', 'Other Feedback', 'Monthly Trend', 'Weekly Summary',
                'Mabinti Centre',
            ], $workbook->getSheetNames());
            $this->assertSame(1, $workbook->getSheetByName('Overview')->getChartCount());
            $this->assertSame(1, $workbook->getSheetByName('Collection Means')->getChartCount());
            $this->assertSame('COLLECTION MEANS', $workbook->getSheetByName('Collection Means')->getCell('A4')->getValue());
            $this->assertSame(1, $workbook->getSheetByName('General Feedback')->getChartCount());
            $this->assertSame(3, $workbook->getSheetByName('OPD Feedback')->getChartCount());
            $this->assertSame(1, $workbook->getSheetByName('OTD Feedback')->getChartCount());
            $this->assertSame(1, $workbook->getSheetByName('Monthly Trend')->getChartCount());
            $opdSheet = $workbook->getSheetByName('OPD Feedback');
            $opdLabels = array_column($opdSheet->rangeToArray('A1:A' . $opdSheet->getHighestRow()), 0);
            $this->assertContains('OPD NEUTRAL FEEDBACK', $opdLabels);
            $this->assertSame('0B8A38', $opdSheet->getChartCollection()[0]->getPlotAreaOrThrow()->getPlotGroupByIndex(0)->getPlotValuesByIndex(0)->getFillColor());
            $this->assertSame('DC3545', $opdSheet->getChartCollection()[1]->getPlotAreaOrThrow()->getPlotGroupByIndex(0)->getPlotValuesByIndex(0)->getFillColor());
            $this->assertSame('64748B', $opdSheet->getChartCollection()[2]->getPlotAreaOrThrow()->getPlotGroupByIndex(0)->getPlotValuesByIndex(0)->getFillColor());
            $this->assertSame('maxMin', $opdSheet->getChartCollection()[0]->getChartAxisX()->getAxisOptionsProperty('orientation'));
            $this->assertSame('minMax', $opdSheet->getChartCollection()[0]->getChartAxisY()->getAxisOptionsProperty('orientation'));
            $this->assertSame('Location', $workbook->getSheetByName('Weekly Summary')->getCell('D4')->getValue());
            $this->assertSame('Satisfied?', $workbook->getSheetByName('Weekly Summary')->getCell('L4')->getValue());
            $this->assertNull($workbook->getSheetByName('Weekly Summary')->getCell('A9')->getValue());
            $trendRow = now()->month + 4;
            $this->assertSame(1, $workbook->getSheetByName('Monthly Trend')->getCell('B' . $trendRow)->getValue());
            $this->assertSame(2, $workbook->getSheetByName('Monthly Trend')->getCell('C' . $trendRow)->getValue());
            $this->assertSame(1, $workbook->getSheetByName('Monthly Trend')->getCell('D' . $trendRow)->getValue());
            $this->assertTrue(is_numeric($workbook->getSheetByName('Overview')->getCell('C6')->getValue()));
            $this->assertSame('0.0%', $workbook->getSheetByName('Overview')->getStyle('C6')->getNumberFormat()->getFormatCode());
        } finally {
            if (isset($workbook)) $workbook->disconnectWorksheets();
            @unlink($path);
        }
    }

    private function feedback(string $sentiment, string $category, string $theme, string $reference, string $location = 'hq'): Feedback
    {
        return Feedback::create([
            'reference_no' => $reference,
            'service_category' => $category,
            'feedback_type' => $sentiment === 'positive' ? 'compliment' : ($sentiment === 'negative' ? 'complaint' : 'suggestion'),
            'theme' => $theme,
            'sentiment' => $sentiment,
            'message' => 'Analytics export test feedback.',
            'source' => 'walk_in',
            'location' => $location,
            'consent_given' => true,
        ]);
    }
}
