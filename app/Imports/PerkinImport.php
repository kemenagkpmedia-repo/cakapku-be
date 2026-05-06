<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Models\Perkin;
use App\Models\SasaranKegiatan;
use App\Models\Iksk;

class PerkinImport implements ToCollection, WithStartRow
{
    private $periodeId;
    private $userId;
    private $perkinId = null;
    private $currentSasaranId = null;

    public function __construct($periodeId, $userId)
    {
        $this->periodeId = $periodeId;
        $this->userId = $userId;
    }

    public function startRow(): int
    {
        return 2; // Assuming row 1 is header
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Mapping based on template image:
            // 0 => No (A)
            // 1 => Sasaran Kegiatan (B)
            // 2 => Indikator Kinerja / IKSK (C)
            // 3 => Target Vol (D)
            // 4 => Target Satuan (E)

            $sasaranKegiatanText = $row[1];
            $indikatorKinerja = $row[2];
            $targetVol = $row[3];
            $targetSatuan = $row[4];

            // 1. Create the Perkin Header if it hasn't been created yet
            // We'll use the first Sasaran Kegiatan as a hint for the Perkin name, 
            // or just a generic name. For better UX, let's use a generic name with date.
            if ($this->perkinId === null && (!empty($sasaranKegiatanText) || !empty($indikatorKinerja))) {
                $perkin = Perkin::create([
                    'nama_perkin' => 'Perjanjian Kinerja ' . date('Y-m-d H:i'),
                    'id_periode'  => $this->periodeId,
                    'status'      => true,
                    'created_by'  => $this->userId,
                ]);
                $this->perkinId = $perkin->id;
            }

            // 2. If Sasaran Kegiatan is provided, we create a new SK record
            if (!empty($sasaranKegiatanText)) {
                $sk = SasaranKegiatan::create([
                    'id_perkin'    => $this->perkinId,
                    'nama_sasaran' => $sasaranKegiatanText,
                ]);
                $this->currentSasaranId = $sk->id;
            }

            // 3. Create the IKSK bound to the current SK.
            if ($this->currentSasaranId && !empty($indikatorKinerja)) {
                Iksk::create([
                    'id_sasaran_kegiatan' => $this->currentSasaranId,
                    'indikator'           => $indikatorKinerja,
                    'target_vol'          => $targetVol,
                    'target_satuan'       => $targetSatuan,
                ]);
            }
        }
    }
}
