<?php

namespace App\Http\Controllers\Api;

use App\Models\Satker;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;

class SatkerController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/satkers",
     *     tags={"Satker"},
     *     summary="Get list of Satker",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="List of Satker"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function index()
    {
        try {
            return response()->json(Satker::with('pimpinan')->get());
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data Satker.',
                'error'   => $e->getMessage(),
            ], 500);
        }
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
     *     @OA\Response(response="201", description="Satker created successfully"),
     *     @OA\Response(response="422", description="Validation error"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nama_satker' => 'required|string|max:255',
                'id_pimpinan' => 'nullable|integer|exists:users,id',
            ]);

            $satker = Satker::create($data);

            return response()->json($satker, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal menyimpan Satker. Terjadi kesalahan pada database.',
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
     *     @OA\Response(response="200", description="Satker updated successfully"),
     *     @OA\Response(response="404", description="Satker not found"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $satker = Satker::findOrFail($id);
            $satker->update($request->all());

            return response()->json($satker);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Satker dengan ID ' . $id . ' tidak ditemukan.',
            ], 404);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal memperbarui Satker.',
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
     *     path="/api/satkers/{id}",
     *     tags={"Satker"},
     *     summary="Delete Satker",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Satker deleted successfully"),
     *     @OA\Response(response="404", description="Satker not found"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function destroy($id)
    {
        try {
            $satker = Satker::findOrFail($id);
            $satker->delete();

            return response()->json(['message' => 'Satker berhasil dihapus.']);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Satker dengan ID ' . $id . ' tidak ditemukan.',
            ], 404);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal menghapus Satker. Mungkin masih ada user yang terkait.',
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
