<?php

namespace App\Http\Controllers\Api;

use App\Models\Iksk;
use Illuminate\Http\Request;

class IkskController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/iksks",
     *     tags={"IKSK"},
     *     summary="Get list of IKSK",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="List of IKSK")
     * )
     */
    public function index()
    {
        return response()->json(Iksk::with('perkin')->get());
    }

    /**
     * @OA\Post(
     *     path="/api/iksks",
     *     tags={"IKSK"},
     *     summary="Create new IKSK",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id_perkin", "indikator"},
     *             @OA\Property(property="id_perkin", type="integer"),
     *             @OA\Property(property="indikator", type="string"),
     *             @OA\Property(property="target_vol", type="string"),
     *             @OA\Property(property="target_satuan", type="string")
     *         )
     *     ),
     *     @OA\Response(response="201", description="IKSK created successfully")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_perkin' => 'required|integer|exists:perkins,id',
            'indikator' => 'required|string',
            'target_vol' => 'nullable|string',
            'target_satuan' => 'nullable|string',
        ]);

        $iksk = Iksk::create($data);

        return response()->json($iksk, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/iksks/{id}",
     *     tags={"IKSK"},
     *     summary="Update IKSK",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="indikator", type="string"),
     *             @OA\Property(property="target_vol", type="string"),
     *             @OA\Property(property="target_satuan", type="string")
     *         )
     *     ),
     *     @OA\Response(response="200", description="IKSK updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $iksk = Iksk::findOrFail($id);
        $data = $request->validate([
            'indikator' => 'nullable|string',
            'target_vol' => 'nullable|string',
            'target_satuan' => 'nullable|string',
        ]);

        $iksk->update($data);

        return response()->json($iksk);
    }

    /**
     * @OA\Delete(
     *     path="/api/iksks/{id}",
     *     tags={"IKSK"},
     *     summary="Delete IKSK",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="IKSK deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $iksk = Iksk::findOrFail($id);
        $iksk->delete();

        return response()->json(['message' => 'IKSK deleted successfully']);
    }
}
