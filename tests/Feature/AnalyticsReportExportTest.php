<?php

namespace Tests\Feature;

use App\Models\Feedback;
use App\Models\Department;
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
                'Feedback Details', 'Mabinti Centre',
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
            $detailsSheet = $workbook->getSheetByName('Feedback Details');
            $this->assertSame('POSITIVE AND NEGATIVE FEEDBACK DETAILS', $detailsSheet->getCell('A1')->getValue());
            $this->assertStringContainsString('Positive: 1', $detailsSheet->getCell('A2')->getValue());
            $this->assertStringContainsString('Negative: 2', $detailsSheet->getCell('A2')->getValue());
            $this->assertSame(7, $detailsSheet->getHighestRow());
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

    public function test_dashboard_tables_share_the_exact_date_month_and_year_filters(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
            'is_first_user' => true,
        ]);

        $june15 = $this->feedback('positive', 'opd', 'client_experience', 'CCBRT-FILTER-JUNE-15');
        $june15->forceFill(['created_at' => '2026-06-15 10:00:00', 'updated_at' => '2026-06-15 10:00:00'])->save();

        $june16 = $this->feedback('negative', 'ipd', 'waiting_time', 'CCBRT-FILTER-JUNE-16');
        $june16->forceFill(['created_at' => '2026-06-16 10:00:00', 'updated_at' => '2026-06-16 10:00:00'])->save();

        $july = $this->feedback('neutral', 'theatre', 'client_satisfaction', 'CCBRT-FILTER-JULY');
        $july->forceFill(['created_at' => '2026-07-01 10:00:00', 'updated_at' => '2026-07-01 10:00:00'])->save();

        $exactDate = $this->actingAs($admin)->get(route('reports.analytics', ['date' => '2026-06-15']));
        $exactDate->assertOk()
            ->assertSee('CCBRT-FILTER-JUNE-15')
            ->assertDontSee('CCBRT-FILTER-JUNE-16')
            ->assertDontSee('CCBRT-FILTER-JULY');

        $monthAndYear = $this->actingAs($admin)->get(route('reports.analytics', ['month' => 6, 'year' => 2026]));
        $monthAndYear->assertOk()
            ->assertSee('CCBRT-FILTER-JUNE-15')
            ->assertSee('CCBRT-FILTER-JUNE-16')
            ->assertDontSee('CCBRT-FILTER-JULY');

        $dateRange = $this->actingAs($admin)->get(route('reports.analytics', [
            'date_from' => '2026-06-15',
            'date_to' => '2026-06-16',
        ]));
        $dateRange->assertOk()
            ->assertSee('CCBRT-FILTER-JUNE-15')
            ->assertSee('CCBRT-FILTER-JUNE-16')
            ->assertDontSee('CCBRT-FILTER-JULY');
    }

    public function test_consolidated_export_uses_all_submitted_analytics_filters(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
            'is_first_user' => true,
        ]);

        $selectedDepartment = Department::create(['name' => 'Selected Department', 'is_active' => true]);
        $otherDepartment = Department::create(['name' => 'Other Department', 'is_active' => true]);

        $makeRecord = function (
            string $reference,
            string $createdAt,
            string $source,
            string $location,
            int $departmentId
        ): void {
            $feedback = $this->feedback('positive', 'opd', 'client_experience', $reference, $location);
            $feedback->forceFill([
                'source' => $source,
                'department_id' => $departmentId,
                'message' => $reference,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ])->save();
        };

        $makeRecord('MATCHES-ALL-FILTERS', '2026-06-15 10:00:00', 'portal', 'hq', $selectedDepartment->id);
        $makeRecord('WRONG-MONTH', '2026-07-15 10:00:00', 'portal', 'hq', $selectedDepartment->id);
        $makeRecord('WRONG-SOURCE', '2026-06-15 10:00:00', 'walk_in', 'hq', $selectedDepartment->id);
        $makeRecord('WRONG-LOCATION', '2026-06-15 10:00:00', 'portal', 'moshi', $selectedDepartment->id);
        $makeRecord('WRONG-DEPARTMENT', '2026-06-15 10:00:00', 'portal', 'hq', $otherDepartment->id);

        $response = $this->actingAs($admin)->get(route('reports.analytics.export.excel', [
            'date_from' => '2026-06-10',
            'date_to' => '2026-06-20',
            'year' => 2026,
            'source' => 'portal',
            'location' => 'hq',
            'department_id' => $selectedDepartment->id,
        ]));

        $response->assertOk();
        $path = tempnam(sys_get_temp_dir(), 'analytics-filtered-export-');
        file_put_contents($path, $response->getContent());

        try {
            $workbook = IOFactory::load($path);
            $sheet = $workbook->getSheetByName('Weekly Summary');

            $this->assertSame('MATCHES-ALL-FILTERS', $sheet->getCell('F5')->getValue());
            $this->assertNull($sheet->getCell('F6')->getValue());
            $this->assertStringContainsString('From: 10 Jun 2026', $sheet->getCell('A2')->getValue());
            $this->assertStringContainsString('To: 20 Jun 2026', $sheet->getCell('A2')->getValue());
            $this->assertStringContainsString('Year: 2026', $sheet->getCell('A2')->getValue());
            $this->assertStringContainsString('Source: Portal', $sheet->getCell('A2')->getValue());
            $this->assertStringContainsString('Location:', $sheet->getCell('A2')->getValue());
            $this->assertStringContainsString('Department: Selected Department', $sheet->getCell('A2')->getValue());
        } finally {
            if (isset($workbook)) $workbook->disconnectWorksheets();
            @unlink($path);
        }
    }

    public function test_monthly_report_modal_and_export_use_the_selected_submission_month(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
            'is_first_user' => true,
        ]);

        $june = $this->feedback('positive', 'opd', 'client_experience', 'MONTHLY-JUNE');
        $june->forceFill([
            'message' => 'MONTHLY-JUNE',
            'created_at' => '2026-06-15 10:00:00',
            'updated_at' => '2026-06-15 10:00:00',
        ])->save();

        $july = $this->feedback('negative', 'ipd', 'waiting_time', 'MONTHLY-JULY');
        $july->forceFill([
            'message' => 'MONTHLY-JULY',
            'created_at' => '2026-07-01 10:00:00',
            'updated_at' => '2026-07-01 10:00:00',
        ])->save();

        $dashboard = $this->actingAs($admin)->get(route('reports.analytics'));
        $dashboard->assertOk()
            ->assertSee('Monthly Report')
            ->assertSee('monthlyReportModal')
            ->assertSee(route('reports.analytics.export.monthly'));

        $response = $this->actingAs($admin)->get(route('reports.analytics.export.monthly', [
            'month' => 6,
            'year' => 2026,
        ]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString(
            'CCBRT-Monthly-Report-June-2026-',
            $response->headers->get('content-disposition') ?? ''
        );

        $path = tempnam(sys_get_temp_dir(), 'monthly-report-');
        file_put_contents($path, $response->getContent());

        try {
            $workbook = IOFactory::load($path);
            $this->assertContains('Monthly Summary', $workbook->getSheetNames());
            $this->assertNotContains('Weekly Summary', $workbook->getSheetNames());

            $sheet = $workbook->getSheetByName('Monthly Summary');
            $this->assertSame('MONTHLY-JUNE', $sheet->getCell('F5')->getValue());
            $this->assertNull($sheet->getCell('F6')->getValue());
            $this->assertStringContainsString('Month: June', $sheet->getCell('A2')->getValue());
            $this->assertStringContainsString('Year: 2026', $sheet->getCell('A2')->getValue());

            $details = $workbook->getSheetByName('Feedback Details');
            $this->assertSame('MONTHLY-JUNE', $details->getCell('B5')->getValue());
            $this->assertNull($details->getCell('B6')->getValue());
            $this->assertStringContainsString('Positive: 1', $details->getCell('A2')->getValue());
            $this->assertStringContainsString('Negative: 0', $details->getCell('A2')->getValue());
        } finally {
            if (isset($workbook)) $workbook->disconnectWorksheets();
            @unlink($path);
        }
    }

    public function test_monthly_report_requires_both_month_and_year(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('reports.analytics.export.monthly', ['month' => 6]));

        $response->assertRedirect();
        $response->assertSessionHasErrors('year');
    }

    public function test_weekly_excel_report_includes_positive_and_negative_feedback_details(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        foreach ([
            ['positive', 'WEEKLY-POSITIVE'],
            ['negative', 'WEEKLY-NEGATIVE'],
            ['neutral', 'WEEKLY-NEUTRAL'],
        ] as [$sentiment, $reference]) {
            $feedback = $this->feedback($sentiment, 'opd', 'client_experience', $reference);
            $feedback->forceFill([
                'message' => $reference,
                'created_at' => '2026-09-10 09:00:00',
                'updated_at' => '2026-09-10 09:00:00',
            ])->save();
        }

        $response = $this->actingAs($admin)->get(route('reports.weekly.export.excel', [
            'month' => 9,
            'year' => 2026,
        ]));

        $response->assertOk();
        $path = tempnam(sys_get_temp_dir(), 'weekly-details-');
        file_put_contents($path, $response->getContent());

        try {
            $workbook = IOFactory::load($path);
            $this->assertSame(['Weekly Submissions', 'Feedback Details'], $workbook->getSheetNames());
            $details = $workbook->getSheetByName('Feedback Details');
            $references = array_column($details->rangeToArray('B5:B7'), 0);

            $this->assertContains('WEEKLY-POSITIVE', $references);
            $this->assertContains('WEEKLY-NEGATIVE', $references);
            $this->assertNotContains('WEEKLY-NEUTRAL', $references);
            $this->assertStringContainsString('Positive: 1', $details->getCell('A2')->getValue());
            $this->assertStringContainsString('Negative: 1', $details->getCell('A2')->getValue());
        } finally {
            if (isset($workbook)) $workbook->disconnectWorksheets();
            @unlink($path);
        }
    }

    public function test_quarterly_report_has_overview_comparison_and_mitigation_sheets(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $april = $this->feedback('positive', 'opd', 'client_experience', 'QUARTER-APRIL');
        $april->forceFill(['created_at' => '2026-04-05 09:00:00', 'updated_at' => '2026-04-05 09:00:00'])->save();

        $may = $this->feedback('negative', 'opd', 'waiting_time', 'QUARTER-MAY');
        $may->forceFill([
            'message' => 'Patients experienced a long waiting time.',
            'status' => 'under_review',
            'created_at' => '2026-05-12 10:00:00',
            'updated_at' => '2026-05-12 10:00:00',
        ])->save();

        $june = $this->feedback('neutral', 'ipd', 'client_satisfaction', 'QUARTER-JUNE');
        $june->forceFill(['created_at' => '2026-06-20 11:00:00', 'updated_at' => '2026-06-20 11:00:00'])->save();

        $july = $this->feedback('negative', 'opd', 'billing_issues', 'QUARTER-JULY');
        $july->forceFill(['created_at' => '2026-07-01 08:00:00', 'updated_at' => '2026-07-01 08:00:00'])->save();

        $dashboard = $this->actingAs($admin)->get(route('reports.analytics'));
        $dashboard->assertOk()
            ->assertSee('Quarterly Report')
            ->assertSee('quarterlyReportModal')
            ->assertSee(route('reports.analytics.export.quarterly'));

        $response = $this->actingAs($admin)->get(route('reports.analytics.export.quarterly', [
            'quarter' => 2,
            'year' => 2026,
        ]));

        $response->assertOk();
        $this->assertStringContainsString('CCBRT-Quarterly-Report-Q2-2026-', $response->headers->get('content-disposition') ?? '');

        $path = tempnam(sys_get_temp_dir(), 'quarterly-report-');
        file_put_contents($path, $response->getContent());

        try {
            $reader = IOFactory::createReader('Xlsx');
            $reader->setIncludeCharts(true);
            $workbook = $reader->load($path);

            $this->assertSame(['Q2 Overview', 'Annual Comparison', 'Mitigation Q2 2026'], $workbook->getSheetNames());
            $overview = $workbook->getSheetByName('Q2 Overview');
            $this->assertSame(1, $overview->getCell('B4')->getValue());
            $this->assertSame(1, $overview->getCell('C5')->getValue());
            $this->assertSame(1, $overview->getCell('E6')->getValue());
            $this->assertSame(1, $overview->getChartCount());

            $comparison = $workbook->getSheetByName('Annual Comparison');
            $this->assertSame(1, $comparison->getCell('D4')->getValue());
            $this->assertSame(1, $comparison->getCell('D17')->getValue());

            $mitigation = $workbook->getSheetByName('Mitigation Q2 2026');
            $this->assertSame('Patients experienced a long waiting time.', $mitigation->getCell('B2')->getValue());
            $this->assertSame('Under review', $mitigation->getCell('D2')->getValue());
            $this->assertSame('Q3 2026', $mitigation->getCell('G2')->getValue());
            $this->assertStringNotContainsString('QUARTER-JULY', $mitigation->getCell('B2')->getValue());
        } finally {
            if (isset($workbook)) $workbook->disconnectWorksheets();
            @unlink($path);
        }
    }

    public function test_quarterly_report_requires_valid_quarter_and_year(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->get(route('reports.analytics.export.quarterly', [
            'quarter' => 5,
        ]));

        $response->assertRedirect();
        $response->assertSessionHasErrors(['quarter', 'year']);
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
