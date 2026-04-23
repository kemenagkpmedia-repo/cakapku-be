<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Satker;

class SatkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Satker::create([
            'nama_satker' => 'Satker Pusat',
            'id_pimpinan' => null, // Will be updated by UserSeeder
        ]);

        Satker::create([
            'nama_satker' => 'Satker Daerah',
            'id_pimpinan' => null,
        ]);
    }
}
