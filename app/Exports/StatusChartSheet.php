<?php

namespace App\Exports;

use App\Models\Anggota;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class StatusChartSheet implements WithTitle, WithEvents
{
    public function title(): string
    {
        return 'Status Chart';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $statusCounts = [
                    'Aktif' => 0,
                    'Nonaktif' => 0,
                    'Pending' => 0,
                ];

                $anggotaList = Anggota::with('subKlasifikasis')->get();
                foreach ($anggotaList as $anggota) {
                    foreach ($anggota->subKlasifikasis as $sub) {
                        $status = ucfirst(strtolower($sub->pivot->status ?? 'Pending'));
                        if (isset($statusCounts[$status])) {
                            $statusCounts[$status]++;
                        }
                    }
                }

                // Tulis data ke sheet
                $sheet->setCellValue('A1', 'Status');
                $sheet->setCellValue('B1', 'Jumlah');

                $row = 2;
                foreach ($statusCounts as $status => $jumlah) {
                    $sheet->setCellValue("A$row", $status);
                    $sheet->setCellValue("B$row", $jumlah);
                    $row++;
                }

                // Chart
                $dataSeriesLabels = [
                    new DataSeriesValues('String', "'Status Chart'!\$B\$1", null, 1),
                ];

                $xAxisTickValues = [
                    new DataSeriesValues('String', "'Status Chart'!\$A\$2:\$A\$4", null, 3),
                ];

                $dataSeriesValues = [
                    new DataSeriesValues('Number', "'Status Chart'!\$B\$2:\$B\$4", null, 3),
                ];

                $series = new DataSeries(
                    DataSeries::TYPE_PIECHART,
                    null,
                    range(0, count($dataSeriesValues) - 1),
                    [],
                    $xAxisTickValues,
                    $dataSeriesValues
                );

                $plotArea = new PlotArea(null, [$series]);
                $legend = new Legend(Legend::POSITION_RIGHT, null, false);
                $title = new Title('Statistik Status SBU');

                $chart = new Chart(
                    'Status Chart',
                    $title,
                    $legend,
                    $plotArea,
                    true,
                    0,
                    null,
                    null
                );

                $chart->setTopLeftPosition('D2');
                $chart->setBottomRightPosition('L20');

                $sheet->addChart($chart);
            }
        ];
    }
}
