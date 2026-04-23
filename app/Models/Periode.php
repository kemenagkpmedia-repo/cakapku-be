<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun',
        'status',
    ];

    public function perkins()
    {
        return $this->hasMany(Perkin::class, 'id_periode');
    }
}
