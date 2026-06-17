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
        'parent_id',
        'level',
    ];

    public function parent()
    {
        return $this->belongsTo(Satker::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Satker::class, 'parent_id');
    }

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
