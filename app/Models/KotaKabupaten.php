<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KotaKabupaten extends Model
{
    protected $table = 'kota_kabupatens';
    protected $fillable = ['nama'];

    public function anggotas(): HasMany
    {
        return $this->hasMany(Anggota::class);
    }
}
