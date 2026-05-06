<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SasaranKegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_perkin',
        'nama_sasaran',
    ];

    public function perkin()
    {
        return $this->belongsTo(Perkin::class, 'id_perkin');
    }

    public function iksks()
    {
        return $this->hasMany(Iksk::class, 'id_sasaran_kegiatan');
    }
}
