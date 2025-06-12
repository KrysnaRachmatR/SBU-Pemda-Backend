<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\KotaKabupaten;

class KotaKabupatenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $data = [
            ['nama' => 'Kota Pontianak'],
            ['nama' => 'Kota Singkawang'],
            ['nama' => 'Kabupaten Sambas'],
            ['nama' => 'Kabupaten Bengkayang'],
            ['nama' => 'Kabupaten Landak'],
            ['nama' => 'Kabupaten Mempawah'],
            ['nama' => 'Kabupaten Sanggau'],
            ['nama' => 'Kabupaten Sekadau'],
            ['nama' => 'Kabupaten Sintang'],
            ['nama' => 'Kabupaten Kapuas Hulu'],
            ['nama' => 'Kabupaten Melawi'],
            ['nama' => 'Kabupaten Ketapang'],
            ['nama' => 'Kabupaten Kayong Utara'],
        ];

        DB::table('kota_kabupatens')->insert($data);
    }
}
