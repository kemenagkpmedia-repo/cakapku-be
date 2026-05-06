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
            'id_sasaran_kegiatan' => 1,
            'indikator' => 'Persentase penyelesaian laporan tepat waktu',
            'target_vol' => '100',
            'target_satuan' => '%'
        ]);

        Iksk::create([
            'id_sasaran_kegiatan' => 1,
            'indikator' => 'Indeks Kepuasan Masyarakat',
            'target_vol' => '85',
            'target_satuan' => 'Poin'
        ]);

        Iksk::create([
            'id_sasaran_kegiatan' => 2,
            'indikator' => 'Persentase penyuluh agama berkategori baik',
            'target_vol' => '90',
            'target_satuan' => '%'
        ]);
    }
}
