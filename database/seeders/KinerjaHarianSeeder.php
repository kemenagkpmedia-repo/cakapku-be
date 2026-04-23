<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KinerjaHarian;

class KinerjaHarianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KinerjaHarian::create([
            'id_user' => 4, // Mapping to user@cakapku.test
            'tanggal' => date('Y-m-d'),
            'id_iksk' => 1,
            'uraian_pekerjaan' => 'Mengerjakan laporan harian bagian keuangan',
            'status_kehadiran' => 'Hadir'
        ]);
    }
}
