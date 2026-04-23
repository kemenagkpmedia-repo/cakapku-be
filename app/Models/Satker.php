<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Satker extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_satker',
        'id_pimpinan',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_satker');
    }

    public function pimpinan()
    {
        return $this->belongsTo(User::class, 'id_pimpinan');
    }

    public function perkins()
    {
        return $this->belongsToMany(Perkin::class, 'perkin_satker', 'id_satker', 'id_perkin');
    }
}
