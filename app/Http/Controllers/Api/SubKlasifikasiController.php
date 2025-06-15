<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubKlasifikasi;
use App\Models\Klasifikasi;
use App\Models\Kbli;
use Illuminate\Http\Request;

class SubKlasifikasiController extends Controller
{
    public function index()
    {
        return SubKlasifikasi::with('klasifikasi', 'kblis')->get();
    }

   public function getTahunByKlasifikasi($klasifikasiId)
    {
        $tahunList = SubKlasifikasi::where('klasifikasi_id', $klasifikasiId)
            ->pluck('tahun')
            ->unique()
            ->sort()
            ->values();

        return response()->json($tahunList);
    }


    public function filter($tahun, $klasifikasiId)
    {
        $subKlasifikasi = SubKlasifikasi::with('kblis')
            ->where('klasifikasi_id', $klasifikasiId)
            ->where('tahun', $tahun)
            ->get(['id', 'nama', 'kode_sub_klasifikasi', 'tahun']);

        return response()->json($subKlasifikasi);
    }




   public function store(Request $request)
    {
        $validated = $request->validate([
            'klasifikasi_id' => 'required|exists:klasifikasis,id',
            'kode_sub_klasifikasi' => 'required|string|max:50',
            'nama' => 'required|string|max:255',
            'tahun' => 'required|integer',
            'kblis' => 'nullable|array',
            'kblis.*.kode' => 'required|string',
        ]);

        // Buat Sub Klasifikasi
        $sub = SubKlasifikasi::create([
            'klasifikasi_id' => $validated['klasifikasi_id'],
            'kode_sub_klasifikasi' => $validated['kode_sub_klasifikasi'], 
            'nama' => $validated['nama'],
            'tahun' => $validated['tahun']
        ]);

        if (!empty($validated['kblis'])) {
            foreach ($validated['kblis'] as $kbliInput) {
                // Cek apakah KBLI sudah ada berdasarkan kode
                $kbli = Kbli::firstOrCreate(
                    ['kode' => $kbliInput['kode']],
                );

                // Hubungkan ke Sub Klasifikasi lewat pivot
                $sub->kblis()->syncWithoutDetaching([$kbli->id]);
            }
        }

        return response()->json(['message' => 'Sub Klasifikasi berhasil ditambahkan', 'data' => $sub], 201);
    }

    public function show($id)
    {
        return SubKlasifikasi::with('klasifikasi', 'kblis')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $sub = SubKlasifikasi::findOrFail($id);

        $validated = $request->validate([
            'klasifikasi_id' => 'sometimes|exists:klasifikasis,id',
            'kode_sub_klasifikasi' => 'sometimes|string|max:50',
            'nama' => 'sometimes|string|max:255',
            'tahun' => 'sometimes|integer',
            'kblis' => 'nullable|array',
            'kblis.*.kode' => 'required|string',
        ]);

        // Update data utama Sub Klasifikasi
        $sub->update($validated);

        // Jika ada kbli baru yang ingin dihubungkan
        if (!empty($validated['kblis'])) {
            foreach ($validated['kblis'] as $kbliInput) {
                $kbli = Kbli::firstOrCreate([
                    'kode' => $kbliInput['kode']
                ]);

                // Hubungkan ke Sub Klasifikasi tanpa melepas relasi lama
                $sub->kblis()->syncWithoutDetaching([$kbli->id]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Sub Klasifikasi berhasil diperbarui',
            'data' => $sub->load('kblis')
        ]);
    }

    public function destroy($id)
    {
        $sub = SubKlasifikasi::findOrFail($id);
        $sub->kblis()->delete();
        $sub->delete();

        return response()->json(['message' => 'Sub Klasifikasi dan KBLI-nya berhasil dihapus']);
    }
}
