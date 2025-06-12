<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Klasifikasi;
use Illuminate\Http\Request;


class KlasifikasiController extends Controller

{
    public function index()
    {
        return Klasifikasi::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255'
        ]);

        $klasifikasi = Klasifikasi::create($validated);

        return response()->json(['message' => 'Klasifikasi berhasil ditambahkan', 'data' => $klasifikasi], 201);
    }

    public function show($id)
    {
        $klasifikasi = Klasifikasi::findOrFail($id);
        return response()->json($klasifikasi);
    }

    public function update(Request $request, $id)
    {
        $klasifikasi = Klasifikasi::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255'
        ]);

        $klasifikasi->update($validated);

        return response()->json(['message' => 'Klasifikasi berhasil diupdate']);
    }

    public function destroy($id)
    {
        $klasifikasi = Klasifikasi::findOrFail($id);
        $klasifikasi->delete();

        return response()->json(['message' => 'Klasifikasi berhasil dihapus']);
    }
}
