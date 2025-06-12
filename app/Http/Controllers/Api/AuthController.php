<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        // Cari user berdasarkan username
        $admin = Admin::where('username', $credentials['username'])->first();

        // Periksa apakah admin ada dan password cocok
        if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        // Hapus token lama jika ingin membatasi hanya 1 sesi
        $admin->tokens()->delete();

        // Buat token baru
        $expiresAt = now()->addHours(6);
        $token = $admin->createToken('Admin Token', ['admin:access'])->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil sebagai admin.',
            'token' => $token,
            'expires_at' => $expiresAt,
            'user' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'username' => $admin->username,
                'role' => 'admin'
            ],
        ], 200);
    }

     public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Tidak ada pengguna yang sedang login'], 401);
        }

        $user->tokens()->delete();

        return response()->json(['message' => 'Logout berhasil'], 200);
    }

}
