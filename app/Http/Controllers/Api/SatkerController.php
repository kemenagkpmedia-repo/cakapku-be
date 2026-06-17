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
            return response()->json(Satker::with(['pimpinan', 'parent'])->get());
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
            if (!$request->user()->isActiveRole('SUPER ADMIN', $request)) {
                return response()->json(['message' => 'Hanya Super Admin yang dapat menambah Satker.'], 403);
            }
            $data = $request->validate([
                'nama_satker' => 'required|string|max:255',
                'id_pimpinan' => 'nullable|integer|exists:users,id',
                'parent_id' => 'nullable|integer|exists:satkers,id',
                'level' => 'nullable|integer|min:0',
            ]);

            // Automatically set level based on parent if level is not provided
            if (!isset($data['level']) || $data['level'] === null) {
                if (isset($data['parent_id']) && $data['parent_id']) {
                    $parent = Satker::find($data['parent_id']);
                    $data['level'] = $parent ? $parent->level + 1 : 0;
                } else {
                    $data['level'] = 0;
                }
            }

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
            if (!$request->user()->isActiveRole('SUPER ADMIN', $request)) {
                return response()->json(['message' => 'Hanya Super Admin yang dapat mengubah Satker.'], 403);
            }
            $satker = Satker::findOrFail($id);
            
            $data = $request->validate([
                'nama_satker' => 'sometimes|required|string|max:255',
                'id_pimpinan' => 'nullable|integer|exists:users,id',
                'parent_id' => 'nullable|integer|exists:satkers,id',
                'level' => 'nullable|integer|min:0',
            ]);

            // Automatically recalculate level if parent_id changed and level is not provided
            if (isset($data['parent_id']) && (!isset($data['level']) || $data['level'] === null)) {
                $parent = Satker::find($data['parent_id']);
                $data['level'] = $parent ? $parent->level + 1 : 0;
            }

            $satker->update($data);

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
    public function destroy(Request $request, $id)
    {
        try {
            if (!$request->user()->isActiveRole('SUPER ADMIN', $request)) {
                return response()->json(['message' => 'Hanya Super Admin yang dapat menghapus Satker.'], 403);
            }
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
