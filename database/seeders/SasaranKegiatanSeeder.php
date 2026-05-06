<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SasaranKegiatan;

class SasaranKegiatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SasaranKegiatan::create([
            'id_perkin' => 1,
            'nama_sasaran' => 'Meningkatnya jaminan beragama, toleransi, dan cinta kemanusiaan umat beragama',
        ]);

        SasaranKegiatan::create([
            'id_perkin' => 1,
            'nama_sasaran' => 'Meningkatnya kualitas layanan keagamaan yang profesional',
        ]);
    }
}
