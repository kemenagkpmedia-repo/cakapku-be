<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/dashboard/bawahan",
     *     tags={"Dashboard Pimpinan"},
     *     summary="View subordinates' work",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="List of subordinates and their Kinerja")
     * )
     */
    public function pekerjaanBawahan(Request $request)
    {
        $pimpinan = $request->user();
        
        // Pimpinan sees users in the satker they lead
        $satker = $pimpinan->satker_dipimpin;
        if (!$satker) {
            return response()->json(['message' => 'Anda bukan pimpinan satker'], 403);
        }

        $bawahan = User::where('id_satker', $satker->id)
            ->with(['kinerja_harians.iksk'])
            ->get();

        return response()->json($bawahan);
    }
}
