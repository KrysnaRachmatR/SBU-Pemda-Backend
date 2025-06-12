<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Anggota;
use Carbon\Carbon;

class AddAnggotaController extends Controller
{
   public function store(Request $request)
    {
        $validated = $request->validate([
            'sub_klasifikasi_ids' => 'required|array',
            'sub_klasifikasi_ids.*' => 'exists:sub_klasifikasis,id',
            'tanggal_pendaftaran' => 'required|date',
            'kota_kabupaten_id' => 'required|exists:kota_kabupatens,id',
            'nama_perusahaan' => 'required|string|max:255',
            'nama_penanggung_jawab' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'npwp' => 'required|string|max:500',
            'nib' => 'required|string|max:500',
            'email' => 'required|email|max:255',
            'no_telp' => 'nullable|string|max:20',
        ]);

        $tanggalPendaftaran = \Carbon\Carbon::parse($validated['tanggal_pendaftaran']);
        $masaBerlakuSampai = $tanggalPendaftaran->copy()->addYears(3)->subDay();
        $today = \Carbon\Carbon::today();

        $anggota = Anggota::firstOrCreate(
            ['nama_perusahaan' => $validated['nama_perusahaan']],
            [
                'kota_kabupaten_id' => $validated['kota_kabupaten_id'],
                'nama_penanggung_jawab' => $validated['nama_penanggung_jawab'],
                'alamat' => $validated['alamat'],
                'npwp' => $validated['npwp'],
                'nib' => $validated['nib'],
                'email' => $validated['email'],
                'no_telp' => $validated['no_telp'] ?? null,
            ]
        );

        $existingSubIds = $anggota->subKlasifikasis()->pluck('sub_klasifikasis.id')->toArray();
        $newSubIds = array_diff($validated['sub_klasifikasi_ids'], $existingSubIds);

        if (empty($newSubIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Anggota sudah memiliki semua sub klasifikasi tersebut.',
            ], 409);
        }

        // Di sini bagian yang perlu kamu update:
        $pivotData = [];
        foreach ($newSubIds as $subId) {
            if ($today->greaterThanOrEqualTo($masaBerlakuSampai)) {
                $status = 'nonaktif';
            } elseif ($today->diffInMonths($masaBerlakuSampai, false) <= 3) {
                $status = 'pending';
            } else {
                $status = 'aktif';
            }

            $pivotData[$subId] = [
                'tanggal_pendaftaran' => $tanggalPendaftaran,
                'masa_berlaku_sampai' => $masaBerlakuSampai,
                'status' => $status,  // Ini tambahan supaya status tersimpan di pivot
            ];
        }

        $anggota->subKlasifikasis()->attach($pivotData);

        return response()->json([
            'success' => true,
            'message' => 'Sub klasifikasi berhasil ditambahkan dengan tanggal pendaftaran dan status.',
            'data' => $anggota->load('subKlasifikasis'),
        ]);
    }

    public function destroy($id)
    {
        $anggota = Anggota::find($id);

        if (!$anggota) {
            return response()->json([
                'success' => false,
                'message' => 'Anggota tidak ditemukan.',
            ], 404);
        }

        // Hapus relasi ke sub klasifikasi terlebih dahulu (pivot)
        $anggota->subKlasifikasis()->detach();

        // Hapus anggota dari tabel anggota
        $anggota->delete();

        return response()->json([
            'success' => true,
            'message' => 'Anggota dan relasi sub klasifikasinya berhasil dihapus.',
        ]);
    }

    public function update(Request $request, $id)
    {
        $anggota = Anggota::find($id);

        if (!$anggota) {
            return response()->json([
                'success' => false,
                'message' => 'Anggota tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'kota_kabupaten_id' => 'required|exists:kota_kabupatens,id',
            'nama_perusahaan' => 'required|string|max:255',
            'nama_penanggung_jawab' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'npwp' => 'required|string|max:500',
            'nib' => 'required|string|max:500',
            'email' => 'required|email|max:255',
            'no_telp' => 'nullable|string|max:20',
        ]);

        // Cek apakah nama perusahaan diubah menjadi milik perusahaan lain
        $cekNamaPerusahaan = Anggota::where('nama_perusahaan', $validated['nama_perusahaan'])
            ->where('id', '!=', $id)
            ->first();

        if ($cekNamaPerusahaan) {
            return response()->json([
                'success' => false,
                'message' => 'Nama perusahaan sudah digunakan oleh anggota lain.',
            ], 409);
        }

        $anggota->update([
            'kota_kabupaten_id' => $validated['kota_kabupaten_id'],
            'nama_perusahaan' => $validated['nama_perusahaan'],
            'nama_penanggung_jawab' => $validated['nama_penanggung_jawab'],
            'alamat' => $validated['alamat'],
            'npwp' => $validated['npwp'],
            'nib' => $validated['nib'],
            'email' => $validated['email'],
            'no_telp' => $validated['no_telp'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data anggota berhasil diperbarui.',
            'data' => $anggota,
        ]);
    }

    public function updateSubKlasifikasi(Request $request, $anggotaId, $subKlasifikasiId)
    {
        $anggota = Anggota::find($anggotaId);
        if (!$anggota) {
            return response()->json([
                'success' => false,
                'message' => 'Anggota tidak ditemukan.',
            ], 404);
        }

        if (!$anggota->subKlasifikasis()->where('sub_klasifikasis.id', $subKlasifikasiId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Sub klasifikasi tidak dimiliki oleh anggota ini.',
            ], 404);
        }

        $validated = $request->validate([
            'tanggal_pendaftaran' => 'required|date',
        ]);

        $tanggalPendaftaran = \Carbon\Carbon::parse($validated['tanggal_pendaftaran']);
        $masaBerlakuSampai = $tanggalPendaftaran->copy()->addYears(3)->subDay();
        $today = \Carbon\Carbon::today();

        if ($today->greaterThanOrEqualTo($masaBerlakuSampai)) {
            $status = 'nonaktif';
        } elseif ($today->diffInMonths($masaBerlakuSampai, false) <= 3) {
            $status = 'pending';
        } else {
            $status = 'aktif';
        }

        $anggota->subKlasifikasis()->updateExistingPivot($subKlasifikasiId, [
            'tanggal_pendaftaran' => $tanggalPendaftaran,
            'masa_berlaku_sampai' => $masaBerlakuSampai,
            'status' => $status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sub klasifikasi anggota berhasil diperbarui.',
            'data' => $anggota->load('subKlasifikasis'),
        ]);
    }
}
