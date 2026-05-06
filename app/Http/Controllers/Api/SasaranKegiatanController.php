<?php

namespace App\Http\Controllers\Api;

use App\Models\SasaranKegiatan;
use Illuminate\Http\Request;
use Exception;

class SasaranKegiatanController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $query = SasaranKegiatan::with(['perkin', 'iksks']);

            // Filter Perkin yang aktif dan Periode yang aktif
            $query->whereHas('perkin', function($q) {
                $q->where('status', true);
                $q->whereHas('periode', function($sq) {
                    $sq->where('status', true);
                });
            });

            // Filter Satker jika bukan admin
            if (!$user->hasRole('ADMIN')) {
                $query->whereHas('perkin.satkers', function ($q) use ($user) {
                    $q->where('satkers.id', $user->id_satker);
                });
            }

            return response()->json($query->get());
        } catch (Exception $e) {
            return response()->json(['message' => 'Gagal mengambil data Sasaran Kegiatan.', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'id_perkin' => 'required|exists:perkins,id',
                'nama_sasaran' => 'required|string',
            ]);
            $sk = SasaranKegiatan::create($data);
            return response()->json($sk, 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Gagal menyimpan Sasaran Kegiatan.', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            return response()->json(SasaranKegiatan::with(['perkin', 'iksks'])->findOrFail($id));
        } catch (Exception $e) {
            return response()->json(['message' => 'Sasaran Kegiatan tidak ditemukan.'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $sk = SasaranKegiatan::findOrFail($id);
            $sk->update($request->all());
            return response()->json($sk);
        } catch (Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui Sasaran Kegiatan.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $sk = SasaranKegiatan::findOrFail($id);
            $sk->delete();
            return response()->json(['message' => 'Sasaran Kegiatan berhasil dihapus.']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Gagal menghapus Sasaran Kegiatan.'], 500);
        }
    }
}
