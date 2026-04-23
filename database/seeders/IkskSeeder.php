<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Iksk;

class IkskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Iksk::create([
            'id_perkin' => 1,
            'indikator' => 'Persentase penyelesaian laporan tepat waktu'
        ]);

        Iksk::create([
            'id_perkin' => 1,
            'indikator' => 'Indeks Kepuasan Masyarakat'
        ]);
    }
}
