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
        'foto',
    ];

    protected $appends = [
        'sub_unit',
        'atasan_user',
        'foto_url',
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

    public function getSubUnitAttribute()
    {
        return $this->satker ? $this->satker->nama_satker : null;
    }

    public function getFotoUrlAttribute()
    {
        if ($this->foto) {
            return url('storage/' . $this->foto);
        }
        return null;
    }

    public function getAtasanUserAttribute()
    {
        $satker = $this->satker;
        if (!$satker) {
            return null;
        }

        // Jika user saat ini bukan pimpinan satker tersebut, atasannya adalah pimpinan satker tersebut
        if ($satker->id_pimpinan && $satker->id_pimpinan != $this->id) {
            return User::find($satker->id_pimpinan);
        }

        // Jika user saat ini adalah pimpinan satker tersebut, atasannya adalah pimpinan dari parent satker
        $parent = $satker->parent;
        while ($parent) {
            if ($parent->id_pimpinan) {
                return User::find($parent->id_pimpinan);
            }
            $parent = $parent->parent;
        }

        return null;
    }

    public function kinerja_harians()
    {
        return $this->hasMany(KinerjaHarian::class, 'id_user');
    }

    public function satker_dipimpin()
    {
        return $this->hasOne(Satker::class, 'id_pimpinan');
    }

    /**
     * Role yang sedang aktif untuk request saat ini.
     * Tidak disimpan di database, hanya di memori selama request.
     */
    public $active_role = null;

    /**
     * Get UI configuration based on user role
     */
    public function getFrontendConfig($requestedRole = null)
    {
        $allRoles = $this->getRoleNames()->map(fn($r) => strtoupper($r))->toArray();

        if ($requestedRole) {
            $role = strtoupper($requestedRole);
            // Pastikan role yang diminta memang dimiliki oleh user
            if (!in_array($role, $allRoles) && !empty($allRoles)) {
                $role = in_array('USER', $allRoles) ? 'USER' : strtoupper($allRoles[0]);
            }
        } else {
            // Prioritas: active_role property > default logic
            $role = $this->active_role ?: (in_array('USER', $allRoles) ? 'USER' : strtoupper($allRoles[0] ?? 'USER'));
        }

        // Simpan ke property agar sinkron
        $this->active_role = $role;

        $config = [
            'active_role' => $role,
            'all_roles' => $allRoles,
            'menus' => [],
            'allowed_roles' => [],
            'dashboard_path' => '/login',
        ];

        // 1. Allowed Roles for User Management (Tetap pakai active role untuk filter menu)
        if ($role === 'SUPER ADMIN') {
            $config['allowed_roles'] = [
                ['label' => 'User', 'value' => 'USER'],
                ['label' => 'Operator', 'value' => 'OPERATOR'],
                ['label' => 'Pimpinan', 'value' => 'PIMPINAN'],
                ['label' => 'Admin', 'value' => 'ADMIN'],
                ['label' => 'Super Admin', 'value' => 'SUPER ADMIN'],
            ];
        } elseif ($role === 'ADMIN') {
            $config['allowed_roles'] = [
                ['label' => 'User', 'value' => 'USER'],
                ['label' => 'Operator', 'value' => 'OPERATOR'],
                ['label' => 'Pimpinan', 'value' => 'PIMPINAN'],
            ];
        }

        // Common menus for ALL ROLES (Self performance reporting)
        $selfKinerjaMenus = [
            ['to' => '/user/kinerja', 'icon' => 'FileText', 'label' => 'Input Kinerja'],
            ['to' => '/user/riwayat', 'icon' => 'CheckSquare', 'label' => 'Riwayat Kinerja'],
            ['to' => '/user/export', 'icon' => 'BarChart3', 'label' => 'Export LKB'],
        ];

        $commonMenus = [
            ['to' => '/user/biodata', 'icon' => 'User', 'label' => 'Biodata Pegawai'],
        ];

        // 2. Navigation Menus based on ACTIVE ROLE
        switch ($role) {
            case 'SUPER ADMIN':
                $config['dashboard_path'] = '/admin/users';
                $config['menus'] = [
                    ['to' => '/admin/users', 'icon' => 'Users', 'label' => 'Manajemen User'],
                    ['to' => '/admin/satker', 'icon' => 'Building', 'label' => 'Manajemen Satker'],
                    ['to' => '/operator/periode', 'icon' => 'Calendar', 'label' => 'Manajemen Periode'],
                    [
                        'label' => 'Perjanjian Kinerja',
                        'icon' => 'FileText',
                        'children' => [
                            ['to' => '/operator/perkin', 'icon' => 'Upload', 'label' => 'Import Perkin'],
                            ['to' => '/operator/sk', 'icon' => 'Target', 'label' => 'Sasaran Kegiatan (SK)'],
                            ['to' => '/operator/iksk', 'icon' => 'Activity', 'label' => 'Indikator Kinerja (IKSK)'],
                        ]
                    ],
                    ['to' => '/operator/perkin-satker', 'icon' => 'Building', 'label' => 'Plotting Satker'],
                    ...$commonMenus
                ];
                break;

            case 'ADMIN':
                $config['dashboard_path'] = '/admin/users';
                $config['menus'] = [
                    ['to' => '/admin/users', 'icon' => 'Users', 'label' => 'Manajemen User'],
                    ['to' => '/admin/satker', 'icon' => 'Building', 'label' => 'Manajemen Satker'],
                    ...$commonMenus
                ];
                break;

            case 'OPERATOR':
                $config['dashboard_path'] = '/operator/perkin-satker';
                $config['menus'] = [
                    [
                        'label' => 'Perjanjian Kinerja',
                        'icon' => 'FileText',
                        'children' => [
                            ['to' => '/operator/perkin', 'icon' => 'Upload', 'label' => 'Import Perkin'],
                            ['to' => '/operator/sk', 'icon' => 'Target', 'label' => 'Sasaran Kegiatan (SK)'],
                            ['to' => '/operator/iksk', 'icon' => 'Activity', 'label' => 'Indikator Kinerja (IKSK)'],
                        ]
                    ],
                    ['to' => '/operator/perkin-satker', 'icon' => 'Building', 'label' => 'Plotting Satker'],
                    ['to' => '/operator/export', 'icon' => 'BarChart3', 'label' => 'Export Data'],
                    ...$commonMenus
                ];
                break;

            case 'PIMPINAN':
                $config['dashboard_path'] = '/pimpinan/dashboard';
                $config['menus'] = [
                    ['to' => '/pimpinan/dashboard', 'icon' => 'LayoutDashboard', 'label' => 'Dashboard'],
                    ['to' => '/pimpinan/monitoring', 'icon' => 'Users', 'label' => 'Monitoring Bawahan'],
                    ...$commonMenus
                ];
                break;

            case 'USER':
            default:
                $config['dashboard_path'] = '/user/kinerja';
                $config['menus'] = [
                    ...$selfKinerjaMenus,
                    ...$commonMenus
                ];
                break;
        }

        return $config;
    }
    
    public function isActiveRole($role, $request = null)
    {
        $role = strtoupper($role);
        
        // Prioritas 1: Property yang sudah di-set oleh middleware SetActiveRole
        if ($this->active_role) {
            return strtoupper($this->active_role) === $role;
        }

        // Prioritas 2: Header langsung (jika middleware belum jalan atau instance berbeda)
        $activeRole = $request ? strtoupper($request->header('X-Active-Role')) : null;

        if ($activeRole) {
            return $activeRole === $role && $this->hasRole($role);
        }

        // Fallback: Gunakan logic default yang sama dengan getFrontendConfig
        $allRoles = $this->getRoleNames()->map(fn($r) => strtoupper($r))->toArray();
        $defaultRole = in_array('USER', $allRoles) ? 'USER' : (strtoupper($allRoles[0] ?? 'USER'));
        
        return $defaultRole === $role;
    }
}
