<?php

namespace App\Http\Controllers\Api;

use App\Models\KinerjaHarian;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;

class KinerjaHarianController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/kinerja-harian",
     *     tags={"Kinerja Harian"},
     *     summary="Get list of Kinerja Harian for logged in user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="List of Kinerja Harian"),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['message' => 'Tidak terautentikasi.'], 401);
            }

            // Pimpinan can see all or specific user? Default implementation is seeing own.
            return response()->json(KinerjaHarian::with('iksk')->where('id_user', $user->id)->get());

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data Kinerja Harian.',
                'error'   => $e->getMessage(),
            ], 500);
        }
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
     *     @OA\Response(response="201", description="Created successfully"),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="422", description="Validation error"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['message' => 'Tidak terautentikasi.'], 401);
            }

            $data = $request->validate([
                'tanggal'          => 'required|date',
                'id_iksk'          => 'required|integer|exists:iksks,id',
                'uraian_pekerjaan' => 'required|string',
                'status_kehadiran' => 'required|string',
            ]);

            $data['id_user'] = $user->id;

            $kinerja = KinerjaHarian::create($data);

            return response()->json($kinerja, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal menyimpan Kinerja Harian. Terjadi kesalahan pada database.',
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
     *     @OA\Response(response="200", description="Updated successfully"),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="403", description="Forbidden - not owner"),
     *     @OA\Response(response="404", description="Not found"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['message' => 'Tidak terautentikasi.'], 401);
            }

            $kinerja = KinerjaHarian::where('id', $id)->where('id_user', $user->id)->firstOrFail();
            $kinerja->update($request->all());

            return response()->json($kinerja);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Kinerja Harian tidak ditemukan atau Anda tidak memiliki akses.',
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal memperbarui Kinerja Harian.',
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
     *     path="/api/kinerja-harian/{id}",
     *     tags={"Kinerja Harian"},
     *     summary="Delete Kinerja Harian",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Deleted successfully"),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="404", description="Not found"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['message' => 'Tidak terautentikasi.'], 401);
            }

            $kinerja = KinerjaHarian::where('id', $id)->where('id_user', $user->id)->firstOrFail();
            $kinerja->delete();

            return response()->json(['message' => 'Kinerja Harian berhasil dihapus.']);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Kinerja Harian tidak ditemukan atau Anda tidak memiliki akses.',
            ], 404);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal menghapus Kinerja Harian.',
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
