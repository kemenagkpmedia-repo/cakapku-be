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
        // Level 0
        $kemenag = Satker::create([
            'nama_satker' => 'Kantor Kemenag',
            'id_pimpinan' => null,
            'parent_id' => null,
            'level' => 0,
        ]);

        // Level 1
        $subbagTu = Satker::create([
            'nama_satker' => 'Subbag TU',
            'id_pimpinan' => null,
            'parent_id' => $kemenag->id,
            'level' => 1,
        ]);

        $seksiPenmad = Satker::create([
            'nama_satker' => 'Seksi Pendidikan Madrasah',
            'id_pimpinan' => null,
            'parent_id' => $kemenag->id,
            'level' => 1,
        ]);

        $seksiBimas = Satker::create([
            'nama_satker' => 'Seksi Bimas Islam',
            'id_pimpinan' => null,
            'parent_id' => $kemenag->id,
            'level' => 1,
        ]);

        // Level 2
        Satker::create([
            'nama_satker' => 'MAN 1',
            'id_pimpinan' => null,
            'parent_id' => $seksiPenmad->id,
            'level' => 2,
        ]);

        Satker::create([
            'nama_satker' => 'KUA Kecamatan A',
            'id_pimpinan' => null,
            'parent_id' => $seksiBimas->id,
            'level' => 2,
        ]);
    }
}
