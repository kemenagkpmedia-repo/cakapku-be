<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Satker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Roles
        $roles = [
            'SUPER ADMIN',
            'ADMIN',
            'PIMPINAN',
            'OPERATOR',
            'USER'
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2. Global Super Admin
        $superAdmin = User::create([
            'nama' => 'Master Super Admin',
            'username' => 'admin',
            'email' => 'admin@cakapku.test',
            'password' => Hash::make('password123'),
        ]);
        $superAdmin->assignRole('SUPER ADMIN');

        // 3. Loop Satkers and create role-based users for each
        $satkers = Satker::all();

        foreach ($satkers as $satker) {
            $slug = Str::slug($satker->nama_satker, '_');

            // Create ADMIN for this Satker
            $admin = User::create([
                'id_satker' => $satker->id,
                'nama' => "Admin " . $satker->nama_satker,
                'username' => "admin_" . $slug,
                'email' => "admin." . str_replace('_', '.', $slug) . "@cakapku.test",
                'password' => Hash::make('password123'),
            ]);
            $admin->assignRole('ADMIN');

            // Create PIMPINAN for this Satker
            $pimpinan = User::create([
                'id_satker' => $satker->id,
                'nama' => "Pimpinan " . $satker->nama_satker,
                'username' => "pimpinan_" . $slug,
                'nip' => '198' . rand(0, 9) . '0101201001' . rand(1000, 9999),
                'email' => "pimpinan." . str_replace('_', '.', $slug) . "@cakapku.test",
                'password' => Hash::make('password123'),
                'jabatan' => 'Kepala ' . $satker->nama_satker,
            ]);
            $pimpinan->assignRole('PIMPINAN');

            // Link Satker to this Pimpinan
            $satker->update(['id_pimpinan' => $pimpinan->id]);

            // Create OPERATOR for this Satker
            $operator = User::create([
                'id_satker' => $satker->id,
                'nama' => "Operator " . $satker->nama_satker,
                'username' => "operator_" . $slug,
                'email' => "operator." . str_replace('_', '.', $slug) . "@cakapku.test",
                'password' => Hash::make('password123'),
            ]);
            $operator->assignRole('OPERATOR');

            // Create 2 USERS for this Satker
            for ($i = 1; $i <= 2; $i++) {
                $user = User::create([
                    'id_satker' => $satker->id,
                    'nama' => "Pegawai $i " . $satker->nama_satker,
                    'username' => "user{$i}_" . $slug,
                    'nip' => '199' . rand(0, 9) . '0101202001' . rand(1000, 9999),
                    'email' => "user{$i}." . str_replace('_', '.', $slug) . "@cakapku.test",
                    'password' => Hash::make('password123'),
                    'jabatan' => 'Staf Pelaksana ' . $i,
                ]);
                $user->assignRole('USER');
            }
        }
    }
}
