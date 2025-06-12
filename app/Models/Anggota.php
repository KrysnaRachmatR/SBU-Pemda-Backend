<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Anggota extends Model
{
    protected $fillable = [
        'kota_kabupaten_id',
        'nama_perusahaan',
        'nama_penanggung_jawab',
        'alamat',
        'npwp',
        'nib',
        'email',
        'no_telp'
    ];

    // Relasi many-to-many dengan SubKlasifikasi lewat tabel pivot anggota_sub_klasifikasi
    public function subKlasifikasis()
    {
        return $this->belongsToMany(SubKlasifikasi::class, 'anggota_sub_klasifikasi')
            ->withPivot(['tanggal_pendaftaran', 'masa_berlaku_sampai', 'status'])
            ->withTimestamps();
    }

    // Relasi ke KotaKabupaten
    public function kotaKabupaten(): BelongsTo
    {
        return $this->belongsTo(KotaKabupaten::class);
    }

    /**
     * Helper method untuk mendapatkan status anggota berdasarkan pivot subKlasifikasi
     * Jika ingin hitung status langsung dari tanggal masa berlaku, bisa juga dipakai method ini
     */
    public function getStatusBySubKlasifikasi($subKlasifikasiId)
    {
        $sub = $this->subKlasifikasis()->where('sub_klasifikasi_id', $subKlasifikasiId)->first();

        if (!$sub) {
            return null; // Tidak ada relasi
        }

        // Ambil data pivot
        $masaBerlaku = Carbon::parse($sub->pivot->masa_berlaku_sampai);
        $today = Carbon::today();

        if ($today->greaterThanOrEqualTo($masaBerlaku)) {
            return 'nonaktif';
        }

        if ($today->diffInMonths($masaBerlaku, false) <= 3) {
            return 'pending';
        }

        return 'aktif';
    }
}
