<?php

namespace App\Http\Controllers\Api;

use App\Models\Perkin;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PerkinImport;

class PerkinController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/perkins",
     *     tags={"Perkin"},
     *     summary="Get list of Perkin",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="List of Perkin")
     * )
     */
    public function index()
    {
        return response()->json(Perkin::with(['periode', 'satkers', 'iksks'])->get());
    }

    /**
     * @OA\Post(
     *     path="/api/perkins",
     *     tags={"Perkin"},
     *     summary="Create new Perkin",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nama_perkin", "id_periode"},
     *             @OA\Property(property="nama_perkin", type="string"),
     *             @OA\Property(property="no_sk", type="string"),
     *             @OA\Property(property="id_periode", type="integer"),
     *             @OA\Property(property="status", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response="201", description="Perkin created successfully")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'no_sk' => 'nullable|string|max:255',
            'nama_perkin' => 'required|string|max:255',
            'id_periode' => 'required|integer|exists:periodes,id',
            'status' => 'boolean',
        ]);
        $data['created_by'] = $request->user()->id ?? null;

        $perkin = Perkin::create($data);

        return response()->json($perkin, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/perkins/{id}",
     *     tags={"Perkin"},
     *     summary="Update Perkin",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_perkin", type="string"),
     *             @OA\Property(property="no_sk", type="string"),
     *             @OA\Property(property="status", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Perkin updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        $perkin = Perkin::findOrFail($id);
        $perkin->update($request->all());

        return response()->json($perkin);
    }

    /**
     * @OA\Post(
     *     path="/api/perkins/{id}/assign-satker",
     *     tags={"Perkin"},
     *     summary="Assign Perkin to Satker",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id_satkers"},
     *             @OA\Property(property="id_satkers", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(response="200", description="Assigned successfully")
     * )
     */
    public function assignSatker(Request $request, $id)
    {
        $perkin = Perkin::findOrFail($id);
        $request->validate([
            'id_satkers' => 'required|array',
            'id_satkers.*' => 'integer|exists:satkers,id',
        ]);

        $perkin->satkers()->sync($request->id_satkers);

        return response()->json(['message' => 'Perkin assigned to Satkers successfully']);
    }

    /**
     * @OA\Delete(
     *     path="/api/perkins/{id}",
     *     tags={"Perkin"},
     *     summary="Delete Perkin",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Perkin deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $perkin = Perkin::findOrFail($id);
        $perkin->delete();

        return response()->json(['message' => 'Perkin deleted successfully']);
    }

    /**
     * @OA\Post(
     *     path="/api/perkins/import",
     *     tags={"Perkin"},
     *     summary="Import Perkin and IKSK from Excel",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="id_periode", type="integer", description="ID of the associated Period"),
     *                 @OA\Property(property="file", type="string", format="binary", description="Excel File (.xlsx, .csv)")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Import successful")
     * )
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'id_periode' => 'required|integer|exists:periodes,id',
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        $userId = $request->user()->id ?? null;

        Excel::import(new PerkinImport($request->id_periode, $userId), $request->file('file'));

        return response()->json(['message' => 'Perkin & IKSK imported successfully']);
    }
}
