<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perkin extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_sk',
        'nama_perkin',
        'id_periode',
        'status',
        'created_by',
    ];

    public function periode()
    {
        return $this->belongsTo(Periode::class, 'id_periode');
    }

    public function satkers()
    {
        return $this->belongsToMany(Satker::class, 'perkin_satker', 'id_perkin', 'id_satker');
    }

    public function sasaran_kegiatans()
    {
        return $this->hasMany(SasaranKegiatan::class, 'id_perkin');
    }

    public function iksks()
    {
        return $this->hasManyThrough(Iksk::class, SasaranKegiatan::class, 'id_perkin', 'id_sasaran_kegiatan');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
