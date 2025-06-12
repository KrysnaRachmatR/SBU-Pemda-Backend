<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kbli extends Model
{
    protected $fillable = ['kode', 'nama'];

    public function subKlasifikasis()
    {
        return $this->belongsToMany(SubKlasifikasi::class, 'kbli_sub_klasifikasi');
    }
}
