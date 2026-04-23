<?php

namespace App\Http\Controllers\Api;

use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;

class PeriodeController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/periodes",
     *     tags={"Periode"},
     *     summary="Get list of Periode",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="List of Periode"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function index()
    {
        try {
            return response()->json(Periode::all());
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data Periode.',
                'error'   => $e->getMessage(),
            ], 500);
        }
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
     *     @OA\Response(response="201", description="Periode created successfully"),
     *     @OA\Response(response="422", description="Validation error"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'tahun'  => 'required|string|max:50',
                'status' => 'required|boolean',
            ]);

            $periode = Periode::create($data);

            return response()->json($periode, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal menyimpan Periode. Terjadi kesalahan pada database.',
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
     * @OA\Get(
     *     path="/api/periodes/{id}",
     *     tags={"Periode"},
     *     summary="Get specific Periode",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Periode object"),
     *     @OA\Response(response="404", description="Periode not found"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function show($id)
    {
        try {
            $periode = Periode::findOrFail($id);
            return response()->json($periode);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Periode dengan ID ' . $id . ' tidak ditemukan.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server.',
                'error'   => $e->getMessage(),
            ], 500);
        }
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
     *     @OA\Response(response="200", description="Periode updated successfully"),
     *     @OA\Response(response="404", description="Periode not found"),
     *     @OA\Response(response="422", description="Validation error"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $periode = Periode::findOrFail($id);

            $data = $request->validate([
                'tahun'  => 'nullable|string|max:50',
                'status' => 'nullable|boolean',
            ]);

            $periode->update($data);

            return response()->json($periode);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Periode dengan ID ' . $id . ' tidak ditemukan.',
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal memperbarui Periode.',
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
     *     path="/api/periodes/{id}",
     *     tags={"Periode"},
     *     summary="Delete Periode",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Periode deleted successfully"),
     *     @OA\Response(response="404", description="Periode not found"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function destroy($id)
    {
        try {
            $periode = Periode::findOrFail($id);
            $periode->delete();

            return response()->json(['message' => 'Periode berhasil dihapus.']);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Periode dengan ID ' . $id . ' tidak ditemukan.',
            ], 404);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal menghapus Periode. Mungkin masih ada data Perkin yang terkait.',
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
