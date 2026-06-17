<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Satker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

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

        // Retrieve Satkers
        $kemenag = Satker::where('nama_satker', 'Kantor Kemenag')->first();
        $subbagTuSatker = Satker::where('nama_satker', 'Subbag TU')->first();
        $seksiPenmadSatker = Satker::where('nama_satker', 'Seksi Pendidikan Madrasah')->first();
        $seksiBimasSatker = Satker::where('nama_satker', 'Seksi Bimas Islam')->first();
        $madrasah = Satker::where('nama_satker', 'MAN 1')->first();
        $kua = Satker::where('nama_satker', 'KUA Kecamatan A')->first();

        // 2. Global Super Admin
        $superAdmin = User::create([
            'nama' => 'Master Super Admin',
            'username' => 'admin',
            'email' => 'admin@cakapku.test',
            'password' => Hash::make('password123'),
        ]);
        $superAdmin->assignRole('SUPER ADMIN');

        // ==========================================
        // 3. KANTOR KEMENAG (Top Level & Seksi)
        // ==========================================

        // Kepala Kantor (Top Level Atasan)
        $kepalaKantor = User::create([
            'id_satker' => $kemenag->id,
            'nama' => 'Drs. H. Ahmad Sahal, M.Pd.I',
            'nip' => '197501012000031001',
            'username' => 'kakan',
            'email' => 'kakan@cakapku.test',
            'password' => Hash::make('password123'),
            'jabatan' => 'Kepala Kantor Kemenag',
            'gol_ruang' => 'IV/a',
        ]);
        $kepalaKantor->assignRole('PIMPINAN');
        $kemenag->update(['id_pimpinan' => $kepalaKantor->id]);

        // Operator Kantor Kemenag (di Subbag TU)
        $operatorKemenag = User::create([
            'id_satker' => $subbagTuSatker->id,
            'nama' => 'Operator Kemenag',
            'username' => 'operator_kemenag',
            'email' => 'operator.kemenag@cakapku.test',
            'password' => Hash::make('password123'),
            'jabatan' => 'Pranata Komputer / Operator Satker',
        ]);
        $operatorKemenag->assignRole('OPERATOR');
        $subbagTuSatker->update(['id_pimpinan' => $operatorKemenag->id]);

        // Kepala Seksi Penmad
        $kasiPenmad = User::create([
            'id_satker' => $seksiPenmadSatker->id,
            'nama' => 'H. Muh. Ridwan, S.Ag',
            'nip' => '198002022005011002',
            'username' => 'kasi_penmad',
            'email' => 'kasi.penmad@cakapku.test',
            'password' => Hash::make('password123'),
            'jabatan' => 'Kepala Seksi Pendidikan Madrasah',
            'gol_ruang' => 'III/d',
        ]);
        $kasiPenmad->assignRole('PIMPINAN');
        $seksiPenmadSatker->update(['id_pimpinan' => $kasiPenmad->id]);

        // Staf Seksi Penmad
        $stafPenmad = User::create([
            'id_satker' => $seksiPenmadSatker->id,
            'nama' => 'Rina Wijayanti, A.Md',
            'nip' => '199006062015032001',
            'username' => 'staf_penmad',
            'email' => 'staf.penmad@cakapku.test',
            'password' => Hash::make('password123'),
            'jabatan' => 'Staf Pelaksana Seksi Penmad',
            'gol_ruang' => 'II/c',
        ]);
        $stafPenmad->assignRole('USER');


        // ==========================================
        // 4. MADRASAH (MAN 1)
        // ==========================================

        // Kepala Madrasah
        $kepalaMadrasah = User::create([
            'id_satker' => $madrasah->id,
            'nama' => 'Dr. Hj. Siti Aminah, M.Pd',
            'nip' => '197803032003122001',
            'username' => 'kamad',
            'email' => 'kamad@cakapku.test',
            'password' => Hash::make('password123'),
            'jabatan' => 'Kepala MAN 1',
            'gol_ruang' => 'IV/a',
        ]);
        $kepalaMadrasah->assignRole('PIMPINAN');
        $madrasah->update(['id_pimpinan' => $kepalaMadrasah->id]);

        // Kepala TU Madrasah
        $kepalaTuMadrasah = User::create([
            'id_satker' => $madrasah->id,
            'nama' => 'Budi Santoso, S.Sos',
            'nip' => '198505052010011004',
            'username' => 'ka_tu_madrasah',
            'email' => 'ka.tu.madrasah@cakapku.test',
            'password' => Hash::make('password123'),
            'jabatan' => 'Kepala TU MAN 1',
            'gol_ruang' => 'III/b',
        ]);
        $kepalaTuMadrasah->assignRole('PIMPINAN');

        // Guru Madrasah
        $guruMadrasah = User::create([
            'id_satker' => $madrasah->id,
            'nama' => 'Sri Wahyuni, S.Pd',
            'nip' => '198308082009012002',
            'username' => 'guru_madrasah',
            'email' => 'guru.madrasah@cakapku.test',
            'password' => Hash::make('password123'),
            'jabatan' => 'Guru Madya MAN 1',
            'gol_ruang' => 'III/c',
        ]);
        $guruMadrasah->assignRole('USER');

        // Staf TU Madrasah
        $stafTuMadrasah = User::create([
            'id_satker' => $madrasah->id,
            'nama' => 'Dian Pratama, A.Md.Kom',
            'nip' => '199509092020011006',
            'username' => 'staf_tu_madrasah',
            'email' => 'staf.tu.madrasah@cakapku.test',
            'password' => Hash::make('password123'),
            'jabatan' => 'Staf Administrasi TU MAN 1',
            'gol_ruang' => 'II/c',
        ]);
        $stafTuMadrasah->assignRole('USER');


        // ==========================================
        // 5. KUA (KUA Kecamatan A)
        // ==========================================

        // Kepala KUA
        $kepalaKua = User::create([
            'id_satker' => $kua->id,
            'nama' => 'H. Lukman Hakim, S.Th.I',
            'nip' => '198204042008011003',
            'username' => 'kakua',
            'email' => 'kakua@cakapku.test',
            'password' => Hash::make('password123'),
            'jabatan' => 'Kepala KUA Kecamatan A',
            'gol_ruang' => 'III/c',
        ]);
        $kepalaKua->assignRole('PIMPINAN');
        $kua->update(['id_pimpinan' => $kepalaKua->id]);

        // Staf KUA
        $stafKua = User::create([
            'id_satker' => $kua->id,
            'nama' => 'Joko Susilo, S.H',
            'nip' => '198807072012011005',
            'username' => 'staf_kua',
            'email' => 'staf.kua@cakapku.test',
            'password' => Hash::make('password123'),
            'jabatan' => 'Staf Pelaksana KUA',
            'gol_ruang' => 'III/a',
        ]);
        $stafKua->assignRole('USER');
    }
}
