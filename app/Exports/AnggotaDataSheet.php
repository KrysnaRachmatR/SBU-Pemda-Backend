<?php

namespace App\Exports;

use App\Models\Anggota;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnggotaDataSheet implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        $data = [];

        $anggotaList = Anggota::with(['subKlasifikasis.klasifikasi'])->get();

        foreach ($anggotaList as $anggota) {
            foreach ($anggota->subKlasifikasis as $sub) {
                $status = strtolower($sub->pivot->status ?? '-');

                $data[] = [
                    'Nama Perusahaan'      => $anggota->nama_perusahaan,
                    'Penanggung Jawab'     => $anggota->nama_penanggung_jawab,
                    'Alamat'               => $anggota->alamat,
                    'Email'                => $anggota->email,
                    'Telepon'              => $anggota->no_telp,
                    'Klasifikasi'          => $sub->klasifikasi->nama ?? '-',
                    'Sub Klasifikasi'      => $sub->nama,
                    'Kode SBU'             => $sub->kode_sub_klasifikasi ?? '-',
                    'Tahun KBLI'           => $sub->tahun ?? '-',
                    'Status'               => ucfirst($status),
                    'Tanggal Pendaftaran'  => $sub->pivot->tanggal_pendaftaran ?? '-',
                    'Masa Berlaku Sampai'  => $sub->pivot->masa_berlaku_sampai ?? '-',
                ];
            }
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Nama Perusahaan',
            'Penanggung Jawab',
            'Alamat',
            'Email',
            'Telepon',
            'Klasifikasi',
            'Sub Klasifikasi',
            'Kode SBU',
            'Tahun KBLI',
            'Status',
            'Tanggal Pendaftaran',
            'Masa Berlaku Sampai',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
