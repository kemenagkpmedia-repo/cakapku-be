<?php

namespace App\Http\Controllers\Api;

use App\Models\Periode;
use Illuminate\Http\Request;

class PeriodeController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/periodes",
     *     tags={"Periode"},
     *     summary="Get list of Periode",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="List of Periode")
     * )
     */
    public function index()
    {
        return response()->json(Periode::all());
    }

    /**
     * @OA\Post(
     *     path="/api/periodes",
     *     tags={"Periode"},
     *     summary="Create new Periode",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"tahun","status"},
     *             @OA\Property(property="tahun", type="string"),
     *             @OA\Property(property="status", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response="201", description="Periode created successfully")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tahun' => 'required|string|max:50',
            'status' => 'required|boolean',
        ]);

        $periode = Periode::create($data);

        return response()->json($periode, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/periodes/{id}",
     *     tags={"Periode"},
     *     summary="Get specific Periode",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Periode object")
     * )
     */
    public function show($id)
    {
        $periode = Periode::findOrFail($id);
        return response()->json($periode);
    }

    /**
     * @OA\Put(
     *     path="/api/periodes/{id}",
     *     tags={"Periode"},
     *     summary="Update Periode",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="tahun", type="string"),
     *             @OA\Property(property="status", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Periode updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $periode = Periode::findOrFail($id);
        $data = $request->validate([
            'tahun' => 'nullable|string|max:50',
            'status' => 'nullable|boolean',
        ]);

        $periode->update($data);

        return response()->json($periode);
    }

    /**
     * @OA\Delete(
     *     path="/api/periodes/{id}",
     *     tags={"Periode"},
     *     summary="Delete Periode",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Periode deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $periode = Periode::findOrFail($id);
        $periode->delete();

        return response()->json(['message' => 'Periode deleted successfully']);
    }
}
