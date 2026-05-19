<?php

namespace App\Http\Controllers\Api;

use App\Models\KinerjaHarian;
use App\Models\User;
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

    /**
     * @OA\Get(
     *     path="/api/kinerja-harian/bawahan",
     *     tags={"Kinerja Harian"},
     *     summary="Get list of Kinerja Harian from subordinates",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="List of Kinerja Harian"),
     *     @OA\Response(response="403", description="Forbidden"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function bawahan(Request $request)
    {
        try {
            $pimpinan = $request->user();

            if (!$pimpinan) {
                return response()->json(['message' => 'Tidak terautentikasi.'], 401);
            }

            $query = User::where('id', '!=', $pimpinan->id)
                ->with(['kinerja_harians.iksk.sasaran_kegiatan.perkin'])
                ->orderBy('nama', 'asc');

            if (!$pimpinan->isActiveRole('SUPER ADMIN', $request)) {
                // Pimpinan sees users in the satker they lead
                $satker = $pimpinan->satker_dipimpin;
                if (!$satker) {
                    return response()->json([
                        'message' => 'Akses ditolak. Anda bukan pimpinan satker manapun.',
                    ], 403);
                }
                $query->where('id_satker', $satker->id);
            }

            $users = $query->get();

            return response()->json($users);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data Kinerja Bawahan.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/kinerja-harian/export-pdf",
     *     tags={"Kinerja Harian"},
     *     summary="Export Kinerja Harian to A4 PDF",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="month", in="query", required=true, @OA\Schema(type="string")),
     *     @OA\Parameter(name="year", in="query", required=true, @OA\Schema(type="string")),
     *     @OA\Parameter(name="pegawai_name", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="pegawai_nip", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="pegawai_jabatan", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="atasan_name", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="atasan_nip", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="signature_date", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="fontSize", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="orientation", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="columns", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="Streamed PDF file attachment"),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function exportPdf(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['message' => 'Tidak terautentikasi.'], 401);
            }

            // 1. Validate Query Parameters
            $month = $request->query('month', date('m'));
            $year = $request->query('year', date('Y'));
            $pegawaiName = $request->query('pegawai_name', $user->nama ?: $user->name);
            $pegawaiNip = $request->query('pegawai_nip', $user->nip);
            $pegawaiJabatan = $request->query('pegawai_jabatan', $user->jabatan);
            
            $atasanName = $request->query('atasan_name', '');
            $atasanNip = $request->query('atasan_nip', '');
            $signatureDate = $request->query('signature_date', '');
            $fontSize = $request->query('fontSize', 'small');
            $orientation = $request->query('orientation', 'landscape');
            
            // Decode toggled columns (passed as JSON string or arrays)
            $showColumnsJson = $request->query('columns', '{"status":true,"perkin":true,"iksk":true,"volume":true,"uraian":true}');
            $showColumns = json_decode($showColumnsJson, true) ?: [
                'status' => true,
                'perkin' => true,
                'iksk' => true,
                'volume' => true,
                'uraian' => true
            ];

            $enableAnchorAtasan = filter_var($request->query('enable_anchor_atasan', false), FILTER_VALIDATE_BOOLEAN);
            $anchorAtasanText = $request->query('anchor_atasan_text', '$ttd_atasan');
            $enableAnchorPegawai = filter_var($request->query('enable_anchor_pegawai', false), FILTER_VALIDATE_BOOLEAN);
            $anchorPegawaiText = $request->query('anchor_pegawai_text', '$ttd_pegawai');

            // 2. Fetch satker name
            $satker = \App\Models\Satker::find($user->id_satker ?: $user->satker_id);
            $satkerName = $satker ? $satker->nama_satker : '-';

            // 3. Fetch Kinerja records for this user matching selected month and year
            $records = KinerjaHarian::with(['iksk.sasaran_kegiatan.perkin'])
                ->where('id_user', $user->id)
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->orderBy('tanggal', 'asc')
                ->get();

            // 4. Map month number to Indonesian name
            $monthsIndo = [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ];
            $monthName = isset($monthsIndo[$month]) ? $monthsIndo[$month] : $month;

            // 5. Build rendering view parameters
            $data = [
                'monthName' => $monthName,
                'year' => $year,
                'pegawaiName' => $pegawaiName,
                'pegawaiNip' => $pegawaiNip,
                'pegawaiJabatan' => $pegawaiJabatan,
                'satkerName' => $satkerName,
                'atasanName' => $atasanName,
                'atasanNip' => $atasanNip,
                'signatureDate' => $signatureDate,
                'fontSize' => $fontSize,
                'orientation' => $orientation,
                'showColumns' => $showColumns,
                'records' => $records,
                'enableAnchorAtasan' => $enableAnchorAtasan,
                'anchorAtasanText' => $anchorAtasanText,
                'enableAnchorPegawai' => $enableAnchorPegawai,
                'anchorPegawaiText' => $anchorPegawaiText
            ];

            // 6. Generate pristine A4 PDF using Laravel DomPDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.lkb_pdf', $data);
            $pdf->setPaper('a4', $orientation);

            // Stream download as attachment with custom filename
            $cleanName = str_replace(' ', '_', $pegawaiName);
            $fileName = "LKB_{$monthName}_{$year}_{$cleanName}.pdf";
            
            return $pdf->download($fileName);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal menghasilkan Laporan Kinerja Bulanan.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
