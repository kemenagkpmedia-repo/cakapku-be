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

    /**
     * Get UI configuration based on user role
     */
    public function getFrontendConfig()
    {
        $role = strtoupper($this->getRoleNames()->first() ?? 'USER');
        $config = [
            'role' => $role,
            'menus' => [],
            'allowed_roles' => [],
            'dashboard_path' => '/login',
        ];

        // 1. Allowed Roles for User Management
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

        // 2. Navigation Menus
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
                ];
                break;

            case 'ADMIN':
                $config['dashboard_path'] = '/admin/users';
                $config['menus'] = [
                    ['to' => '/admin/users', 'icon' => 'Users', 'label' => 'Manajemen User'],
                    ['to' => '/admin/satker', 'icon' => 'Building', 'label' => 'Manajemen Satker'],
                ];
                break;

            case 'OPERATOR':
                $config['dashboard_path'] = '/operator/periode';
                $config['menus'] = [
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
                    ['to' => '/operator/export', 'icon' => 'BarChart3', 'label' => 'Export Data'],
                ];
                break;

            case 'PIMPINAN':
                $config['dashboard_path'] = '/pimpinan/dashboard';
                $config['menus'] = [
                    ['to' => '/pimpinan/dashboard', 'icon' => 'LayoutDashboard', 'label' => 'Dashboard'],
                    ['to' => '/pimpinan/monitoring', 'icon' => 'Users', 'label' => 'Monitoring Bawahan'],
                    ['to' => '/user/kinerja', 'icon' => 'FileText', 'label' => 'Input Kinerja'],
                    ['to' => '/user/riwayat', 'icon' => 'CheckSquare', 'label' => 'Riwayat Kinerja'],
                    ['to' => '/user/export', 'icon' => 'BarChart3', 'label' => 'Export LKB'],
                ];
                break;

            case 'USER':
            default:
                $config['dashboard_path'] = '/user/kinerja';
                $config['menus'] = [
                    ['to' => '/user/kinerja', 'icon' => 'FileText', 'label' => 'Input Kinerja'],
                    ['to' => '/user/riwayat', 'icon' => 'CheckSquare', 'label' => 'Riwayat Kinerja'],
                    ['to' => '/user/export', 'icon' => 'BarChart3', 'label' => 'Export LKB'],
                ];
                break;
        }

        return $config;
    }
}
