<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(['message' => 'Token tidak ditemukan'], 401);
        }

        // Ambil data token dari database
        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken) {
            return response()->json(['message' => 'Token tidak valid'], 401);
        }

        // Cek apakah token sudah expired
        if ($accessToken->expires_at && now()->greaterThan($accessToken->expires_at)) {
            return response()->json(['message' => 'Token sudah kedaluwarsa'], 401);
        }

        // Cek apakah user memiliki akses admin
        if (!$request->user()->tokenCan('admin:access')) {
            return response()->json(['message' => 'Unauthorized - Admin Only'], 403);
        }

        return $next($request);
    }
}
