<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Exception;

class DashboardController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/dashboard/bawahan",
     *     tags={"Dashboard Pimpinan"},
     *     summary="View subordinates' work",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="List of subordinates and their Kinerja"),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="403", description="Forbidden - bukan pimpinan satker"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function pekerjaanBawahan(Request $request)
    {
        try {
            $pimpinan = $request->user();

            if (!$pimpinan) {
                return response()->json(['message' => 'Tidak terautentikasi.'], 401);
            }

            // Pimpinan sees users in the satker they lead
            $satker = $pimpinan->satker_dipimpin;
            if (!$satker) {
                return response()->json([
                    'message' => 'Akses ditolak. Anda bukan pimpinan satker manapun.',
                ], 403);
            }

            $bawahan = User::where('id_satker', $satker->id)
                ->with(['kinerja_harians.iksk'])
                ->get();

            return response()->json($bawahan);

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal mengambil data bawahan. Terjadi kesalahan pada database.',
                'error'   => $e->getMessage(),
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
