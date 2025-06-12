<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubKlasifikasi extends Model
{
    protected $table = 'sub_klasifikasis';

    protected $fillable = [
        'klasifikasi_id',
        'kode_sub_klasifikasi',
        'nama',
        'tahun'
    ];

    // Relasi SubKlasifikasi ke Klasifikasi (one-to-many inverse)
    public function klasifikasi(): BelongsTo
    {
        return $this->belongsTo(Klasifikasi::class);
    }

    // Relasi many-to-many ke Anggota via pivot anggota_sub_klasifikasi
    public function anggotas(): BelongsToMany
    {
        return $this->belongsToMany(Anggota::class, 'anggota_sub_klasifikasi')
                    ->withPivot(['tanggal_pendaftaran', 'masa_berlaku_sampai', 'status'])
                    ->withTimestamps();
    }

    // Relasi many-to-many ke Kbli
    public function kblis(): BelongsToMany
    {
        return $this->belongsToMany(Kbli::class, 'kbli_sub_klasifikasi');
    }
}
