<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Anggota;
use App\Models\Klasifikasi;
use App\Models\SubKlasifikasi;
use App\Models\Kbli;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ShowAnggotaController extends Controller
{
    public function show($id)
    {
        $anggota = Anggota::with([
            'subKlasifikasis.klasifikasi',
            'subKlasifikasis.kblis',
            'kotaKabupaten'
        ])->find($id);

        if (!$anggota) {
            return response()->json([
                'success' => false,
                'message' => 'Anggota tidak ditemukan.'
            ], 404);
        }

        // Format sub klasifikasi
        $subKlasifikasiData = $anggota->subKlasifikasis->map(function ($sub) {
            return [
                'nama_klasifikasi' => $sub->klasifikasi->nama ?? null,
                'nama_sub_klasifikasi' => $sub->nama,
                'kode_sub_klasifikasi' => $sub->kode_sub_klasifikasi,
                'tahun' => $sub->tahun,
                'kblis' => $sub->kblis->map(function ($kbli) {
                    return [
                        'kode' => $kbli->kode,
                        'nama' => $kbli->nama
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Detail anggota berhasil ditemukan.',
            'data' => [
                'id' => $anggota->id,
                'nama_perusahaan' => $anggota->nama_perusahaan,
                'nama_penanggung_jawab' => $anggota->nama_penanggung_jawab,
                'alamat' => $anggota->alamat,
                'kota_kabupaten' => $anggota->kotaKabupaten->nama ?? null,
                'npwp' => $anggota->npwp,
                'nib' => $anggota->nib,
                'email' => $anggota->email,
                'no_telp' => $anggota->no_telp,
                'tanggal_terbit' => $anggota->tanggal_pendaftaran,
                'masa_berlaku_sampai' => $anggota->masa_berlaku_sampai,
                'status' => $anggota->status,
                'sub_klasifikasis' => $subKlasifikasiData
            ]
        ], 200);
    }

    public function index()
    {
        $anggotas = Anggota::with([
            'subKlasifikasis.klasifikasi',
            'subKlasifikasis.kblis',
            'kotaKabupaten'
        ])->get();

        if ($anggotas->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada data anggota yang terdaftar.'
            ], 404);
        }

        $data = $anggotas->map(function ($anggota) {
            return [
                'id' => $anggota->id,
                'nama_perusahaan' => $anggota->nama_perusahaan,
                'nama_penanggung_jawab' => $anggota->nama_penanggung_jawab,
                'alamat' => $anggota->alamat,
                'kota_kabupaten' => $anggota->kotaKabupaten->nama ?? null,
                'npwp' => $anggota->npwp,
                'nib' => $anggota->nib,
                'email' => $anggota->email,
                'no_telp' => $anggota->no_telp,
                'sub_klasifikasi' => $anggota->subKlasifikasis->map(function ($sub) {
                    return [
                        'nama_klasifikasi' => $sub->klasifikasi->nama ?? null,
                        'nama_sub_klasifikasi' => $sub->nama ?? null,
                        'kode_sub_klasifikasi' => $sub->kode_sub_klasifikasi ?? null,
                        'kblis' => $sub->kblis->map(function ($kbli) {
                            return [
                                'kode_kbli' => $kbli->kode,
                            ];
                        }),
                        'tanggal_terbit' => $sub->pivot->tanggal_pendaftaran,
                        'masa_berlaku_sampai' => $sub->pivot->masa_berlaku_sampai,
                        'status' => $sub->pivot->status,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar anggota berhasil ditemukan.',
            'data' => $data
        ], 200);
    }

    public function search(Request $request)
    {
        $query = Anggota::with([
            'subKlasifikasis.klasifikasi',
            'subKlasifikasis.kblis',
            'kotaKabupaten'
        ]);

        if ($request->filled('nama_perusahaan')) {
        $nama = strtolower(trim($request->nama_perusahaan));
        $query->whereRaw('LOWER(nama_perusahaan) LIKE ?', ['%' . $nama . '%']);
        }

        if ($request->filled('kode_sbu')) {
            $query->whereHas('subKlasifikasis', function ($q) use ($request) {
                $q->where('kode_sub_klasifikasi', $request->kode_sbu);
            });
        }

        $anggotas = $query->get();

        if ($anggotas->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Anggota tidak ditemukan.'
            ], 404);
        }

        $data = $anggotas->map(function ($anggota) {
            return [
                'id' => $anggota->id,
                'nama_perusahaan' => $anggota->nama_perusahaan,
                'nama_penanggung_jawab' => $anggota->nama_penanggung_jawab,
                'alamat' => $anggota->alamat,
                'kota_kabupaten' => $anggota->kotaKabupaten->nama ?? null,
                'npwp' => $anggota->npwp,
                'nib' => $anggota->nib,
                'email' => $anggota->email,
                'no_telp' => $anggota->no_telp,
                'sub_klasifikasi' => $anggota->subKlasifikasis->map(function ($sub) {
                    return [
                        'nama_klasifikasi' => $sub->klasifikasi->nama ?? null,
                        'nama_sub_klasifikasi' => $sub->nama ?? null,
                        'kode_sub_klasifikasi' => $sub->kode_sub_klasifikasi ?? null,
                        'kblis' => $sub->kblis->map(function ($kbli) {
                            return [
                                'kode_kbli' => $kbli->kode,
                            ];
                        }),
                        'tanggal_terbit' => $sub->pivot->tanggal_pendaftaran,
                        'masa_berlaku_sampai' => $sub->pivot->masa_berlaku_sampai,
                        'status' => $sub->pivot->status,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data anggota ditemukan.',
            'data' => $data
        ]);
    }

    public function anggotaPerKlasifikasi()
    {
        $data = \App\Models\Klasifikasi::with(['subKlasifikasis.anggotas'])->get();

        $result = $data->map(function ($klasifikasi) {
            $subKlasifikasis = $klasifikasi->subKlasifikasis->map(function ($sub) {
                return [
                    'id' => $sub->id,
                    'nama' => $sub->nama,
                    'kode_sub_klasifikasi' => $sub->kode_sub_klasifikasi,
                    'jumlah_anggota' => $sub->anggotas->count(),
                ];
            });

            return [
                'id' => $klasifikasi->id,
                'nama_klasifikasi' => $klasifikasi->nama,
                'total_anggota' => $subKlasifikasis->sum('jumlah_anggota'),
                'sub_klasifikasis' => $subKlasifikasis,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data jumlah anggota per klasifikasi',
            'data' => $result,
        ]);
    }

    public function statistikStatusAnggota()
    {
        // Ambil jumlah masing-masing status dari tabel pivot
        $statusCounts = DB::table('anggota_sub_klasifikasi')
            ->select('status', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        // Pastikan semua status muncul meskipun 0
        $data = [
            'aktif' => $statusCounts['aktif'] ?? 0,
            'pending' => $statusCounts['pending'] ?? 0,
            'nonaktif' => $statusCounts['nonaktif'] ?? 0,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Statistik status anggota berdasarkan masa berlaku',
            'data' => $data,
        ]);
    }

    public function anggotaPerSubKlasifikasi()
    {
        $data = \App\Models\SubKlasifikasi::with('anggotas', 'klasifikasi')->get();

        $result = $data->map(function ($sub) {
            return [
                'id' => $sub->id,
                'nama_sub_klasifikasi' => $sub->nama,
                'kode_sub_klasifikasi' => $sub->kode_sub_klasifikasi,
                'nama_klasifikasi' => $sub->klasifikasi->nama ?? null,
                'jumlah_anggota' => $sub->anggotas->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data jumlah anggota per sub klasifikasi',
            'data' => $result,
        ]);
    }
}
