<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'id_satker',
        'nip',
        'nama',
        'jabatan',
        'gol_ruang',
        'username',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function satker()
    {
        return $this->belongsTo(Satker::class, 'id_satker');
    }

    public function kinerja_harians()
    {
        return $this->hasMany(KinerjaHarian::class, 'id_user');
    }

    public function satker_dipimpin()
    {
        return $this->hasOne(Satker::class, 'id_pimpinan');
    }
}
