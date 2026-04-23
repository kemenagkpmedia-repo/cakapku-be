<?php

namespace App\Http\Controllers\Api;

use App\Models\Iksk;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;

class IkskController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/iksks",
     *     tags={"IKSK"},
     *     summary="Get list of IKSK",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="List of IKSK"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function index()
    {
        try {
            return response()->json(Iksk::with('perkin')->get());
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data IKSK.',
                'error'   => $e->getMessage(),
            ], 500);
        }
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
     *     @OA\Response(response="201", description="IKSK created successfully"),
     *     @OA\Response(response="422", description="Validation error"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'id_perkin'     => 'required|integer|exists:perkins,id',
                'indikator'     => 'required|string',
                'target_vol'    => 'nullable|string',
                'target_satuan' => 'nullable|string',
            ]);

            $iksk = Iksk::create($data);

            return response()->json($iksk, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal menyimpan data IKSK. Terjadi kesalahan pada database.',
                'error'   => $e->getMessage(),
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server.',
                'error'   => $e->getMessage(),
            ], 500);
        }
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
     *     @OA\Response(response="200", description="IKSK updated successfully"),
     *     @OA\Response(response="404", description="IKSK not found"),
     *     @OA\Response(response="422", description="Validation error"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $iksk = Iksk::findOrFail($id);

            $data = $request->validate([
                'indikator'     => 'nullable|string',
                'target_vol'    => 'nullable|string',
                'target_satuan' => 'nullable|string',
            ]);

            $iksk->update($data);

            return response()->json($iksk);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'IKSK dengan ID ' . $id . ' tidak ditemukan.',
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal memperbarui data IKSK.',
                'error'   => $e->getMessage(),
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/iksks/{id}",
     *     tags={"IKSK"},
     *     summary="Delete IKSK",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="IKSK deleted successfully"),
     *     @OA\Response(response="404", description="IKSK not found"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function destroy($id)
    {
        try {
            $iksk = Iksk::findOrFail($id);
            $iksk->delete();

            return response()->json(['message' => 'IKSK berhasil dihapus.']);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'IKSK dengan ID ' . $id . ' tidak ditemukan.',
            ], 404);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal menghapus data IKSK. Mungkin masih ada data terkait.',
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
