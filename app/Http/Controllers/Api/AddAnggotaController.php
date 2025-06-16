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
            'sub_klasifikasi_ids.*' => 'integer|exists:sub_klasifikasis,id',
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

        // Ambil tanggal
        $tanggalPendaftaran = \Carbon\Carbon::parse($validated['tanggal_pendaftaran']);
        $masaBerlakuSampai = $tanggalPendaftaran->copy()->addYears(3)->subDay();
        $today = \Carbon\Carbon::today();

        // Cari atau buat anggota
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

        // 🔁 Ubah nama sub klasifikasi → ID
        $subIds = $validated['sub_klasifikasi_ids'];
        if (empty($subIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada sub klasifikasi yang valid ditemukan.',
            ], 422);
        }

        // Ambil sub klasifikasi yang sudah dimiliki anggota
        $existingSubIds = $anggota->subKlasifikasis()->pluck('sub_klasifikasis.id')->toArray();
        $newSubIds = array_diff($subIds, $existingSubIds);

        if (empty($newSubIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Anggota sudah memiliki semua sub klasifikasi tersebut.',
            ], 409);
        }

        // Siapkan data pivot
        $pivotData = [];
        foreach ($newSubIds as $subId) {
            $status = 'aktif';

            if ($today->greaterThanOrEqualTo($masaBerlakuSampai)) {
                $status = 'nonaktif';
            } elseif ($today->diffInMonths($masaBerlakuSampai, false) <= 3) {
                $status = 'pending';
            }

            $pivotData[$subId] = [
                'tanggal_pendaftaran' => $tanggalPendaftaran,
                'masa_berlaku_sampai' => $masaBerlakuSampai,
                'status' => $status,
            ];
        }

        // Simpan relasi ke pivot table
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

    public function updateSubKlasifikasiTanggal(Request $request, $anggotaId)
{
    $validated = $request->validate([
        'sub_klasifikasi_id' => 'required|integer|exists:sub_klasifikasis,id',
        'tanggal_pendaftaran' => 'required|date',
    ]);

    $anggota = Anggota::find($anggotaId);
    if (!$anggota) {
        return response()->json([
            'success' => false,
            'message' => 'Anggota tidak ditemukan.',
        ], 404);
    }

    $subId = $validated['sub_klasifikasi_id'];

    // Cek apakah sub klasifikasi terhubung dengan anggota
    if (!$anggota->subKlasifikasis()->where('sub_klasifikasis.id', $subId)->exists()) {
        return response()->json([
            'success' => false,
            'message' => 'Sub klasifikasi ini belum terhubung dengan anggota.',
        ], 422);
    }

    // Hitung ulang masa berlaku & status
    $tanggalPendaftaran = \Carbon\Carbon::parse($validated['tanggal_pendaftaran']);
    $masaBerlakuSampai = $tanggalPendaftaran->copy()->addYears(3)->subDay();
    $today = \Carbon\Carbon::today();

    $status = 'aktif';
    if ($today->greaterThanOrEqualTo($masaBerlakuSampai)) {
        $status = 'nonaktif';
    } elseif ($today->diffInMonths($masaBerlakuSampai, false) <= 3) {
        $status = 'pending';
    }

    // Update data pivot
    $anggota->subKlasifikasis()->updateExistingPivot($subId, [
        'tanggal_pendaftaran' => $tanggalPendaftaran,
        'masa_berlaku_sampai' => $masaBerlakuSampai,
        'status' => $status,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Tanggal pendaftaran sub klasifikasi berhasil diperbarui.',
        'data' => $anggota->load('subKlasifikasis'),
    ]);
}
}
