<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KotaKabupaten;

class KotaKabupatenController extends Controller
{
    public function index()
    {
        $kotaKabupaten = KotaKabupaten::all();
        return response()->json($kotaKabupaten);
    }

    public function show($id)
    {
        $kotaKabupaten = KotaKabupaten::findOrFail($id);
        return response()->json($kotaKabupaten);
    }
}