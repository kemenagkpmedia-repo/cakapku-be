<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Perkin;

class PerkinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $perkin = Perkin::create([
            'no_sk' => 'SK-001/2024',
            'nama_perkin' => 'Perjanjian Kinerja Tahun 2024',
            'id_periode' => 1,
            'status' => true,
            'created_by' => 3 // Assuming operator ID is 3 mapping to UserSeeder
        ]);

        // Assign to Satker Pusat & Daerah
        $perkin->satkers()->sync([1, 2]);
    }
}
