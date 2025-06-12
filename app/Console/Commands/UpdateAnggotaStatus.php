<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Anggota; // ✅ Tambahkan ini

class UpdateAnggotaStatus extends Command
{
    protected $signature = 'anggota:update-status';
    protected $description = 'Update status anggota_sub_klasifikasi berdasarkan tanggal pendaftaran (masa berlaku 3 tahun - 1 hari)';

    public function handle()
    {
        $today = Carbon::today();

        Anggota::with('subKlasifikasis')->get()->each(function ($anggota) use ($today) {
            foreach ($anggota->subKlasifikasis as $sub) {
                $tanggalDaftar = Carbon::parse($sub->pivot->tanggal_pendaftaran);
                $masaBerlaku = $tanggalDaftar->copy()->addYears(3)->subDay();

                // Tentukan status
                if ($today->greaterThanOrEqualTo($masaBerlaku)) {
                    $status = 'nonaktif';
                } elseif ($today->diffInMonths($masaBerlaku, false) <= 3) {
                    $status = 'pending';
                } else {
                    $status = 'aktif';
                }

                // Update pivot
                $anggota->subKlasifikasis()->updateExistingPivot($sub->id, [
                    'masa_berlaku_sampai' => $masaBerlaku,
                    'status' => $status,
                ]);
            }
        });

        $this->info('Status dan masa berlaku anggota-sub_klasifikasi telah diperbarui.');
    }
}
