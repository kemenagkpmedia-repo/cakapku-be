<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use App\Models\Perkin;
use App\Models\Iksk;

class PerkinImport implements ToCollection, WithStartRow
{
    private $periodeId;
    private $userId;
    private $currentPerkinId = null;

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
        foreach ($rows as $row) {
            // Mapping based on template image:
            // 0 => No (A)
            // 1 => Sasaran Kegiatan (B)
            // 2 => Indikator Kinerja / IKSK (C)
            // 3 => Target Vol (D)
            // 4 => Target Satuan (E)

            $sasaranKegiatan = $row[1];
            $indikatorKinerja = $row[2];
            $targetVol = $row[3];
            $targetSatuan = $row[4];

            // If Sasaran Kegiatan is provided, we create a new Perkin block
            // This handles the merged-cell like hierarchy.
            if (!empty($sasaranKegiatan)) {
                $perkin = Perkin::create([
                    'nama_perkin' => $sasaranKegiatan,
                    'id_periode' => $this->periodeId,
                    'status' => true,
                    'created_by' => $this->userId,
                ]);
                $this->currentPerkinId = $perkin->id;
            }

            // Create the IKSK bound to the current SK.
            if ($this->currentPerkinId && !empty($indikatorKinerja)) {
                Iksk::create([
                    'id_perkin' => $this->currentPerkinId,
                    'indikator' => $indikatorKinerja,
                    'target_vol' => $targetVol,
                    'target_satuan' => $targetSatuan,
                ]);
            }
        }
    }
}
