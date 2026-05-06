<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iksk extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_sasaran_kegiatan',
        'indikator',
        'target_vol',
        'target_satuan',
    ];

    public function sasaran_kegiatan()
    {
        return $this->belongsTo(SasaranKegiatan::class, 'id_sasaran_kegiatan');
    }

    public function kinerja_harians()
    {
        return $this->hasMany(KinerjaHarian::class, 'id_iksk');
    }
}
