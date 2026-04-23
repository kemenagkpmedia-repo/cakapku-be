<?php

namespace App\Http\Controllers\Api;

use App\Models\KinerjaHarian;
use Illuminate\Http\Request;

class KinerjaHarianController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/kinerja-harian",
     *     tags={"Kinerja Harian"},
     *     summary="Get list of Kinerja Harian for logged in user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="List of Kinerja Harian")
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        
        // Pimpinan can see all or specific user? Default implementation is seeing own.
        return response()->json(KinerjaHarian::with('iksk')->where('id_user', $user->id)->get());
    }

    /**
     * @OA\Post(
     *     path="/api/kinerja-harian",
     *     tags={"Kinerja Harian"},
     *     summary="Create new Kinerja Harian",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"tanggal", "id_iksk", "uraian_pekerjaan", "status_kehadiran"},
     *             @OA\Property(property="tanggal", type="string", format="date"),
     *             @OA\Property(property="id_iksk", type="integer"),
     *             @OA\Property(property="uraian_pekerjaan", type="string"),
     *             @OA\Property(property="status_kehadiran", type="string", description="Hadir/Izin/Sakit dll")
     *         )
     *     ),
     *     @OA\Response(response="201", description="Created successfully")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tanggal' => 'required|date',
            'id_iksk' => 'required|integer|exists:iksks,id',
            'uraian_pekerjaan' => 'required|string',
            'status_kehadiran' => 'required|string',
        ]);

        $data['id_user'] = $request->user()->id;

        $kinerja = KinerjaHarian::create($data);

        return response()->json($kinerja, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/kinerja-harian/{id}",
     *     tags={"Kinerja Harian"},
     *     summary="Update Kinerja Harian",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="uraian_pekerjaan", type="string"),
     *             @OA\Property(property="status_kehadiran", type="string")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $kinerja = KinerjaHarian::where('id', $id)->where('id_user', $request->user()->id)->firstOrFail();
        $kinerja->update($request->all());

        return response()->json($kinerja);
    }

    /**
     * @OA\Delete(
     *     path="/api/kinerja-harian/{id}",
     *     tags={"Kinerja Harian"},
     *     summary="Delete Kinerja Harian",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Deleted successfully")
     * )
     */
    public function destroy(Request $request, $id)
    {
        $kinerja = KinerjaHarian::where('id', $id)->where('id_user', $request->user()->id)->firstOrFail();
        $kinerja->delete();

        return response()->json(['message' => 'Kinerja Harian deleted successfully']);
    }
}
