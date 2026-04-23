<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KinerjaHarian extends Model
{
    use HasFactory;

    protected $table = 'kinerja_harians';

    protected $fillable = [
        'id_user',
        'tanggal',
        'id_iksk',
        'uraian_pekerjaan',
        'status_kehadiran',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function iksk()
    {
        return $this->belongsTo(Iksk::class, 'id_iksk');
    }
}
