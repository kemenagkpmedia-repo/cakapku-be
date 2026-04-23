<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iksk extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_perkin',
        'indikator',
        'target_vol',
        'target_satuan',
    ];

    public function perkin()
    {
        return $this->belongsTo(Perkin::class, 'id_perkin');
    }

    public function kinerja_harians()
    {
        return $this->hasMany(KinerjaHarian::class, 'id_iksk');
    }
}
