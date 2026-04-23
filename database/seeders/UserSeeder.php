<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Satker;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleAdmin = Role::create(['name' => 'ADMIN']);
        $rolePimpinan = Role::create(['name' => 'PIMPINAN']);
        $roleOperator = Role::create(['name' => 'OPERATOR']);
        $roleUser = Role::create(['name' => 'USER']);

        $admin = User::create([
            'nama' => 'Super Admin',
            'username' => 'admin',
            'email' => 'admin@cakapku.test',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole($roleAdmin);

        $pimpinan = User::create([
            'id_satker' => 1,
            'nama' => 'Bapak Pimpinan',
            'username' => 'pimpinan',
            'nip' => '198001012010011001',
            'email' => 'pimpinan@cakapku.test',
            'password' => Hash::make('password123'),
        ]);
        $pimpinan->assignRole($rolePimpinan);

        // Update Satker 1's pimpinan
        Satker::where('id', 1)->update(['id_pimpinan' => $pimpinan->id]);

        $operator = User::create([
            'id_satker' => 1,
            'nama' => 'Mas Operator',
            'username' => 'operator',
            'email' => 'operator@cakapku.test',
            'password' => Hash::make('password123'),
        ]);
        $operator->assignRole($roleOperator);

        $user1 = User::create([
            'id_satker' => 1,
            'nama' => 'User Satker Pusat',
            'username' => 'user1',
            'nip' => '199001012010011002',
            'email' => 'user@cakapku.test',
            'password' => Hash::make('password123'),
        ]);
        $user1->assignRole($roleUser);
    }
}
