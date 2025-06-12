<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\AnggotaExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;

class AnggotaExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new AnggotaDataSheet(),
            new StatusChartSheet(),
        ];
    }
}
