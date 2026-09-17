<?php

namespace App\Services;

use App\Models\Feedback;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;

class QuarterlyFeedbackReport
{
    private const AREAS = [
        'Service Related',
        'Process Related',
        'Human Resource Related',
        'Customer Care Related',
        'Food Service Related',
        'Facility / Environment Related',
        'Other',
    ];

    public function download(int $quarter, int $year): Response
    {
        $spreadsheet = $this->build($quarter, $year);
        $filename = "CCBRT-Quarterly-Report-Q{$quarter}-{$year}-" . now()->format('Ymd-His') . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();
        $spreadsheet->disconnectWorksheets();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function build(int $quarter, int $year): Spreadsheet
    {
        $months = $this->quarterMonths($quarter);
        $yearFeedback = Feedback::query()
            ->with([
                'department.hod', 'assignedTo', 'reviewedBy',
                'patientResponses.sender', 'internalNotes.author', 'escalations.hod',
            ])
            ->whereYear('created_at', $year)
            ->orderBy('created_at')
            ->get();
        $quarterFeedback = $yearFeedback->filter(
            fn (Feedback $feedback) => in_array((int) $feedback->created_at?->month, $months, true)
        )->values();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle("CCBRT Q{$quarter} {$year} Customer Feedback Report")
            ->setSubject('Quarterly customer feedback, category comparison, and mitigation report');

        $this->buildOverviewSheet($spreadsheet, $quarterFeedback, $quarter, $year, $months);
        $this->buildComparisonSheet($spreadsheet, $yearFeedback, $year);
        $this->buildMitigationSheet($spreadsheet, $quarterFeedback, $quarter, $year);
        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function buildOverviewSheet(Spreadsheet $spreadsheet, Collection $feedback, int $quarter, int $year, array $months): void
    {
        $sheet = $spreadsheet->getActiveSheet()->setTitle("Q{$quarter} Overview");
        $this->title($sheet, "A1:F1", "QUARTER {$quarter} {$year} OVERALL CUSTOMER FEEDBACK REPORT");
        $sheet->fromArray(['MONTH', 'POSITIVE', 'NEGATIVE', 'NEGATIVE %', 'NEUTRAL', 'GRAND TOTAL'], null, 'A3');
        $this->header($sheet, 'A3:F3');

        $monthNames = [1=>'JANUARY',2=>'FEBRUARY',3=>'MARCH',4=>'APRIL',5=>'MAY',6=>'JUNE',7=>'JULY',8=>'AUGUST',9=>'SEPTEMBER',10=>'OCTOBER',11=>'NOVEMBER',12=>'DECEMBER'];
        foreach ($months as $index => $month) {
            $row = 4 + $index;
            $records = $feedback->filter(fn (Feedback $item) => (int) $item->created_at?->month === $month);
            $positive = $records->where('sentiment', 'positive')->count();
            $negative = $records->where('sentiment', 'negative')->count();
            $neutral = $records->where('sentiment', 'neutral')->count();
            $total = $records->count();
            $sheet->fromArray([$monthNames[$month], $positive, $negative, $total > 0 ? $negative / $total : 0, $neutral, $total], null, 'A' . $row);
            $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_0);
            $this->bodyRow($sheet, "A{$row}:F{$row}", $index % 2 === 1);
        }

        $totalRow = 7;
        $sheet->fromArray(['GRAND TOTAL', '=SUM(B4:B6)', '=SUM(C4:C6)', '=IF(F7=0,0,C7/F7)', '=SUM(E4:E6)', '=SUM(F4:F6)'], null, 'A' . $totalRow);
        $sheet->getStyle("A{$totalRow}:F{$totalRow}")->getFont()->setBold(true);
        $sheet->getStyle("D{$totalRow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_0);
        $sheet->getStyle("A{$totalRow}:F{$totalRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('94C83D');

        $negativeRate = $feedback->count() > 0 ? $feedback->where('sentiment', 'negative')->count() / $feedback->count() : 0;
        $previousQuarter = $quarter === 1 ? 4 : $quarter - 1;
        $previousYear = $quarter === 1 ? $year - 1 : $year;
        $previousMonths = $this->quarterMonths($previousQuarter);
        $previous = Feedback::query()->whereYear('created_at', $previousYear)->whereIn(\DB::raw('MONTH(created_at)'), $previousMonths)->get();
        $previousRate = $previous->count() > 0 ? $previous->where('sentiment', 'negative')->count() / $previous->count() : 0;

        $sheet->fromArray([
            ["Q{$quarter} {$year} Negative Feedback", $negativeRate],
            ['Standard Maximum Negative', 0.20],
            ["Q{$previousQuarter} {$previousYear} Negative Feedback", $previousRate],
        ], null, 'A10');
        $sheet->getStyle('A10:A12')->getFont()->setBold(true);
        $sheet->getStyle('B10:B12')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_0);
        $sheet->getStyle('A10:B12')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9EEC2');
        $this->allBorders($sheet, 'A10:B12');

        $labels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'Q{$quarter} Overview'!\$B\$3", null, 1),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'Q{$quarter} Overview'!\$C\$3", null, 1),
        ];
        $categories = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'Q{$quarter} Overview'!\$A\$4:\$A\$6", null, 3)];
        $values = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'Q{$quarter} Overview'!\$B\$4:\$B\$6", null, 3),
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'Q{$quarter} Overview'!\$C\$4:\$C\$6", null, 3),
        ];
        $series = new DataSeries(DataSeries::TYPE_BARCHART, DataSeries::GROUPING_CLUSTERED, [0, 1], $labels, $categories, $values);
        $series->setPlotDirection(DataSeries::DIRECTION_COL);
        $chart = new Chart('quarter_overview', new Title("Q{$quarter} CUSTOMER FEEDBACK"), new Legend(Legend::POSITION_BOTTOM), new PlotArea(null, [$series]));
        $chart->setTopLeftPosition('H2')->setBottomRightPosition('P17');
        $sheet->addChart($chart);

        foreach (['A'=>18,'B'=>14,'C'=>14,'D'=>14,'E'=>12,'F'=>14] as $column => $width) $sheet->getColumnDimension($column)->setWidth($width);
        $sheet->freezePane('A4');
        $sheet->getPageSetup()->setOrientation('landscape')->setFitToWidth(1)->setFitToHeight(0);
    }

    private function buildComparisonSheet(Spreadsheet $spreadsheet, Collection $feedback, int $year): void
    {
        $sheet = $spreadsheet->createSheet()->setTitle('Annual Comparison');
        $row = 1;
        foreach (['positive' => 'POSITIVE FEEDBACK', 'negative' => 'NEGATIVE FEEDBACK', 'neutral' => 'NEUTRAL FEEDBACK'] as $sentiment => $title) {
            $this->title($sheet, "A{$row}:I{$row}", "{$title} — {$year}");
            $quarterRow = $row + 1;
            $headerRow = $row + 2;
            $sheet->setCellValue("A{$quarterRow}", 'AREA');
            foreach (range(1, 4) as $q) {
                $column = 2 + (($q - 1) * 2);
                $start = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column);
                $end = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column + 1);
                $sheet->mergeCells("{$start}{$quarterRow}:{$end}{$quarterRow}");
                $sheet->setCellValue("{$start}{$quarterRow}", "Q{$q}");
                $sheet->setCellValue("{$start}{$headerRow}", 'Number of Feedback');
                $sheet->setCellValue("{$end}{$headerRow}", '%');
            }
            $this->subHeader($sheet, "A{$quarterRow}:I{$headerRow}");

            $dataStart = $row + 3;
            foreach (self::AREAS as $areaIndex => $area) {
                $dataRow = $dataStart + $areaIndex;
                $sheet->setCellValue("A{$dataRow}", $area);
                foreach (range(1, 4) as $q) {
                    $quarterRecords = $feedback->filter(fn (Feedback $item) => $this->quarterForMonth((int) $item->created_at?->month) === $q && $item->sentiment === $sentiment);
                    $count = $quarterRecords->filter(fn (Feedback $item) => $this->areaFor($item) === $area)->count();
                    $total = $quarterRecords->count();
                    $column = 2 + (($q - 1) * 2);
                    $sheet->setCellValue([$column, $dataRow], $count);
                    $sheet->setCellValue([$column + 1, $dataRow], $total > 0 ? $count / $total : 0);
                    $sheet->getStyle([$column + 1, $dataRow])->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_0);
                }
                $this->bodyRow($sheet, "A{$dataRow}:I{$dataRow}", $areaIndex % 2 === 1);
            }

            $totalRow = $dataStart + count(self::AREAS);
            $sheet->setCellValue("A{$totalRow}", 'TOTAL');
            foreach (range(1, 4) as $q) {
                $column = 2 + (($q - 1) * 2);
                $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column);
                $pctLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column + 1);
                $sheet->setCellValue("{$letter}{$totalRow}", "=SUM({$letter}{$dataStart}:{$letter}" . ($totalRow - 1) . ')');
                $sheet->setCellValue("{$pctLetter}{$totalRow}", "=IF({$letter}{$totalRow}=0,0,SUM({$pctLetter}{$dataStart}:{$pctLetter}" . ($totalRow - 1) . '))');
                $sheet->getStyle("{$pctLetter}{$totalRow}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_0);
            }
            $sheet->getStyle("A{$totalRow}:I{$totalRow}")->getFont()->setBold(true);
            $this->allBorders($sheet, "A{$quarterRow}:I{$totalRow}");
            $row = $totalRow + 2;
        }

        $sheet->getColumnDimension('A')->setWidth(34);
        foreach (range('B', 'I') as $column) $sheet->getColumnDimension($column)->setWidth(17);
        $sheet->freezePane('B4');
        $sheet->getPageSetup()->setOrientation('landscape')->setFitToWidth(1)->setFitToHeight(0);
    }

    private function buildMitigationSheet(Spreadsheet $spreadsheet, Collection $feedback, int $quarter, int $year): void
    {
        $sheet = $spreadsheet->createSheet()->setTitle("Mitigation Q{$quarter} {$year}");
        $sheet->fromArray(['Areas of Improvement', 'Description', 'Action Taken', 'Current Status', 'Monitoring Method', 'Responsible', 'Timeline'], null, 'A1');
        $this->header($sheet, 'A1:G1');
        $negative = $feedback->where('sentiment', 'negative')->values();
        $nextQuarter = $quarter === 4 ? 1 : $quarter + 1;
        $nextYear = $quarter === 4 ? $year + 1 : $year;

        foreach ($negative as $index => $item) {
            $row = $index + 2;
            $latestEscalation = $item->escalations->sortByDesc('created_at')->first();
            $latestResponse = $item->patientResponses->sortByDesc('created_at')->first();
            $latestNote = $item->internalNotes->sortByDesc('created_at')->first();
            $action = $latestEscalation?->hod_response
                ?: $latestResponse?->content
                ?: $latestNote?->content
                ?: 'Awaiting documented action';
            $responsible = $item->assignedTo?->getFullName()
                ?? $latestEscalation?->hod?->name
                ?? $item->department?->hod?->name
                ?? 'Unassigned';
            $monitoring = $item->getThemeLabel() === '—' ? 'Customer feedback follow-up' : $item->getThemeLabel() . ' feedback trend';
            $status = match ($item->status) {
                'responded' => 'Action communicated / monitoring',
                'closed' => 'Closed / monitoring complete',
                'under_review' => 'Under review',
                default => 'Open / action pending',
            };
            $description = $item->report_excerpt ?: 'No detailed feedback description recorded.';
            $sheet->fromArray([
                $this->areaFor($item), $description, $action, $status,
                $monitoring, $responsible, "Q{$nextQuarter} {$nextYear}",
            ], null, 'A' . $row);
            $this->bodyRow($sheet, "A{$row}:G{$row}", $index % 2 === 1);
            $statusColor = match ($item->status) {
                'responded', 'closed' => '92D050',
                'under_review' => 'FFF200',
                default => 'FF5B5B',
            };
            $sheet->getStyle("D{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($statusColor);
            $sheet->getStyle("B{$row}:F{$row}")->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
        }

        if ($negative->isEmpty()) {
            $sheet->mergeCells('A2:G3');
            $sheet->setCellValue('A2', "No negative feedback was submitted in Q{$quarter} {$year}.");
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        }

        foreach (['A'=>26,'B'=>52,'C'=>52,'D'=>30,'E'=>30,'F'=>26,'G'=>14] as $column => $width) $sheet->getColumnDimension($column)->setWidth($width);
        for ($row = 2; $row <= max(2, $negative->count() + 1); $row++) $sheet->getRowDimension($row)->setRowHeight(58);
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:G' . max(2, $negative->count() + 1));
        $sheet->getPageSetup()->setOrientation('landscape')->setFitToWidth(1)->setFitToHeight(0);
    }

    private function areaFor(Feedback $feedback): string
    {
        $text = strtolower(($feedback->message ?? '') . ' ' . ($feedback->overall_experience ?? '') . ' ' . ($feedback->theme ?? ''));
        if (preg_match('/food|meal|cafe|kitchen|nutrition/', $text)) return 'Food Service Related';

        return match ($feedback->theme) {
            'delayed_slow_service', 'waiting_time', 'billing_issues', 'inadequate_information' => 'Process Related',
            'few_staff' => 'Human Resource Related',
            'customer_care_staff', 'staff_appreciation', 'general_positive_feedback' => 'Customer Care Related',
            'enviro_housekeeping', 'well_equipped' => 'Facility / Environment Related',
            'client_experience', 'client_outcome', 'client_satisfaction', 'medication_issues' => 'Service Related',
            default => 'Other',
        };
    }

    private function quarterMonths(int $quarter): array
    {
        $start = (($quarter - 1) * 3) + 1;
        return [$start, $start + 1, $start + 2];
    }

    private function quarterForMonth(int $month): int
    {
        return (int) ceil($month / 3);
    }

    private function title($sheet, string $range, string $title): void
    {
        $sheet->mergeCells($range);
        $sheet->setCellValue(explode(':', $range)[0], $title);
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'size' => 15, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '065321']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension((int) preg_replace('/\D/', '', explode(':', $range)[0]))->setRowHeight(26);
    }

    private function header($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0B6B2C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
        $this->allBorders($sheet, $range);
    }

    private function subHeader($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'B8D4EA']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
        ]);
    }

    private function bodyRow($sheet, string $range, bool $alternate): void
    {
        if ($alternate) $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F4F8F5');
        $this->allBorders($sheet, $range);
    }

    private function allBorders($sheet, string $range): void
    {
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('64748B');
    }
}
