<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\AnggotaExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;

class AnggotaExportController extends Controller
{
    public function exportExcel()
{
    return Excel::download(new AnggotaExport, 'anggota.xlsx', \Maatwebsite\Excel\Excel::XLSX, [
    'useCharts' => true
]);

}
}
