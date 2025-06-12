<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KlasifikasiController;
use App\Http\Controllers\Api\SubKlasifikasiController;
use App\Http\Controllers\Api\AddAnggotaController;
use App\Http\Controllers\Api\ShowAnggotaController;
use App\Http\Controllers\Api\KbliController;
use App\Http\Controllers\Api\AnggotaExportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // CRUD routes for Klasifikasi
    Route::post('/klasifikasi', [KlasifikasiController::class, 'store']);
    Route::get('/klasifikasi', [KlasifikasiController::class, 'index']);
    Route::get('/klasifikasi/{id}', [KlasifikasiController::class, 'show']);
    Route::put('/klasifikasi/{id}', [KlasifikasiController::class, 'update']);
    Route::delete('/klasifikasi/{id}', [KlasifikasiController::class, 'destroy']);
    
    // CRUD routes for SubKlasifikasi
    Route::post('/sub-klasifikasi', [SubKlasifikasiController::class, 'store']);
    Route::get('/sub-klasifikasi', [SubKlasifikasiController::class, 'index']);
    Route::get('/sub-klasifikasi/{id}', [SubKlasifikasiController::class, 'show']);
    Route::put('/sub-klasifikasi/{id}', [SubKlasifikasiController::class, 'update']);
    Route::delete('/sub-klasifikasi/{id}', [SubKlasifikasiController::class, 'destroy']);

    // CRUD routes for KBLI
    Route::post('/kbli', [KbliController::class, 'store']);
    Route::get('/kbli', [KbliController::class, 'index']);
    Route::get('/kbli/{id}', [KbliController::class, 'show']);
    Route::put('/kbli/{id}', [KbliController::class, 'update']);
    Route::delete('/kbli/{id}', [KbliController::class, 'destroy']);
    
    // Add Anggota
    Route::post('/add-anggota', [AddAnggotaController::class, 'store']);
    Route::put('/add-anggota/{id}', [AddAnggotaController::class, 'update']);
    Route::put('/anggota/{id}/sub-klasifikasi/{subId}', [AddAnggotaController::class, 'updateSubKlasifikasi']);
    Route::delete('/add-anggota/{id}', [AddAnggotaController::class, 'destroy']);

    // Show Anggota
    Route::get('/anggota', [ShowAnggotaController::class, 'index']);
    Route::get('/anggota/{id}', [ShowAnggotaController::class, 'show']);
    Route::get('/anggota/search', [ShowAnggotaController::class, 'search']);
    Route::get('/diagram-klasifikasi-anggota', [ShowAnggotaController::class, 'anggotaPerKlasifikasi']);
    Route::get('/diagram-status-anggota', [ShowAnggotaController::class, 'statistikStatusAnggota']);
    Route::get('/diagram-status-anggotaa', [ShowAnggotaController::class, 'statistikStatussAnggota']);
});

Route::get('/export/anggota/excel', [AnggotaExportController::class, 'exportExcel']);
Route::get('/export/anggota/csv', [AnggotaExportController::class, 'exportCsv']);


