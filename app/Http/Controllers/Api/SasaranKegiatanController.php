<?php

namespace App\Http\Controllers\Api;

use App\Models\SasaranKegiatan;
use Illuminate\Http\Request;
use Exception;

class SasaranKegiatanController extends BaseController
{
    public function index()
    {
        try {
            return response()->json(SasaranKegiatan::with(['perkin', 'iksks'])->get());
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
