<?php

namespace App\Http\Controllers;

use App\Models\Kbli;
use Illuminate\Http\Request;

class KbliController extends Controller
{
    // Tampilkan semua KBLI
    public function index()
    {
        return Kbli::with('subKlasifikasis:id,kode,nama')->get();
    }

    // Simpan KBLI baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|unique:kblis,kode|max:50',
            'nama' => 'required|string|max:255'
        ]);

        $kbli = Kbli::create($validated);

        return response()->json([
            'message' => 'KBLI berhasil ditambahkan',
            'data' => $kbli
        ], 201);
    }

    // Tampilkan detail
    public function show($id)
    {
        $kbli = Kbli::with('subKlasifikasis:id,kode,nama')->findOrFail($id);

        return response()->json($kbli);
    }

    // Update KBLI
    public function update(Request $request, $id)
    {
        $kbli = Kbli::findOrFail($id);

        $validated = $request->validate([
            'kode' => 'sometimes|string|unique:kblis,kode,' . $kbli->id,
            'nama' => 'sometimes|string|max:255'
        ]);

        $kbli->update($validated);

        return response()->json(['message' => 'KBLI berhasil diupdate']);
    }

    // Hapus KBLI
    public function destroy($id)
    {
        $kbli = Kbli::findOrFail($id);
        $kbli->delete();

        return response()->json(['message' => 'KBLI berhasil dihapus']);
    }
}
