<?php

namespace App\Http\Controllers\Api;

use App\Models\Satker;
use Illuminate\Http\Request;

class SatkerController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/satkers",
     *     tags={"Satker"},
     *     summary="Get list of Satker",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="List of Satker")
     * )
     */
    public function index()
    {
        return response()->json(Satker::with('pimpinan')->get());
    }

    /**
     * @OA\Post(
     *     path="/api/satkers",
     *     tags={"Satker"},
     *     summary="Create new Satker",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nama_satker"},
     *             @OA\Property(property="nama_satker", type="string"),
     *             @OA\Property(property="id_pimpinan", type="integer")
     *         )
     *     ),
     *     @OA\Response(response="201", description="Satker created successfully")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_satker' => 'required|string|max:255',
            'id_pimpinan' => 'nullable|integer|exists:users,id',
        ]);

        $satker = Satker::create($data);

        return response()->json($satker, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/satkers/{id}",
     *     tags={"Satker"},
     *     summary="Update Satker",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_satker", type="string"),
     *             @OA\Property(property="id_pimpinan", type="integer")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Satker updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $satker = Satker::findOrFail($id);
        $data = $request->all();
        $satker->update($data);

        return response()->json($satker);
    }

    /**
     * @OA\Delete(
     *     path="/api/satkers/{id}",
     *     tags={"Satker"},
     *     summary="Delete Satker",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Satker deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $satker = Satker::findOrFail($id);
        $satker->delete();

        return response()->json(['message' => 'Satker deleted successfully']);
    }
}
