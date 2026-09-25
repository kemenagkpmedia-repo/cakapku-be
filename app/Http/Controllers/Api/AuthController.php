<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;

class AuthController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="Login user",
     *     description="Login user and returns token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username","password"},
     *             @OA\Property(property="username", type="string", description="Username or NIP", example="admin"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Successful login"),
     *     @OA\Response(response="401", description="Invalid credentials"),
     *     @OA\Response(response="422", description="Validation error"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = User::where('username', $request->username)
                ->orWhere('nip', $request->username)
                ->orWhere('email', $request->username)
                ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Kredensial tidak valid. Periksa username/NIP dan password Anda.',
                ], 401);
            }

            // Token akan kedaluwarsa dalam 24 jam
            $token = $user->createToken('auth_token', ['*'], now()->addHours(24))->plainTextToken;

            // getFrontendConfig() tanpa parameter akan default ke USER
            $config = $user->getFrontendConfig();
            $user->role = $config['active_role'];

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                'config' => $config,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada database.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/me",
     *     tags={"Authentication"},
     *     summary="Get current user info",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="User info with config"),
     *     @OA\Response(response="401", description="Unauthenticated")
     * )
     */
    public function me(Request $request)
    {
        $user = $request->user();

        // Prioritas: query param > X-Active-Role header > default (USER)
        $requestedRole = $request->query('role') ?: $request->header('X-Active-Role');

        // Validasi jika requestedRole ada, pastikan user punya role tersebut
        if ($requestedRole && !$user->hasRole($requestedRole)) {
            $requestedRole = null;
        }

        $config = $user->getFrontendConfig($requestedRole);
        $user->role = $config['active_role'];

        return response()->json([
            'user' => $user,
            'config' => $config,
        ]);
    }

    public function updateFoto(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['message' => 'Tidak terautentikasi.'], 401);
            }

            // Cek jika file terpotong/gagal karena limit php.ini di server
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
                $errCode = $_FILES['foto']['error'];
                if ($errCode === UPLOAD_ERR_INI_SIZE || $errCode === UPLOAD_ERR_FORM_SIZE) {
                    return response()->json([
                        'message' => 'Ukuran file foto melebihi batas upload PHP server (upload_max_filesize). Silakan pilih foto di bawah 2MB atau naikkan upload_max_filesize di php.ini hosting.'
                    ], 422);
                }
            }

            $request->validate([
                'foto' => 'required|image|mimes:jpeg,png,jpg,gif,webp,jfif,heic|max:2048',
            ]);

            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada di storage
                if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                    try {
                        Storage::disk('public')->delete($user->foto);
                    } catch (\Throwable $th) {
                        // Abaikan jika gagal hapus foto lama
                    }
                }

                // Simpan foto baru ke disk 'public' folder 'avatars' (otomatis buat direktori jika belum ada)
                $file = $request->file('foto');
                $filename = $file->store('avatars', 'public');

                if (!$filename) {
                    return response()->json(['message' => 'Gagal menyimpan file foto ke storage server.'], 500);
                }

                // Update kolom foto di DB
                $user->update([
                    'foto' => $filename
                ]);

                // Ambil data user segar dengan appends
                $user->refresh();

                return response()->json([
                    'message' => 'Foto profil berhasil diperbarui.',
                    'foto' => $user->foto,
                    'foto_url' => $user->foto_url
                ], 200);
            }

            return response()->json(['message' => 'File foto tidak terdeteksi.'], 400);

        } catch (ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            return response()->json([
                'message' => $firstError ?: 'File tidak valid. Pastikan format gambar (jpg/png/webp) dan ukuran maksimal 2MB.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Gagal mengunggah foto profil.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/switch-role",
     *     tags={"Authentication"},
     *     summary="Switch active role and get new config",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"role"},
     *             @OA\Property(property="role", type="string", example="ADMIN")
     *         )
     *     ),
     *     @OA\Response(response="200", description="New config returned"),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="403", description="Forbidden")
     * )
     */
    public function switchRole(Request $request)
    {
        $request->validate([
            'role' => 'required|string',
        ]);

        $user = $request->user();
        $requestedRole = strtoupper($request->role);

        if (!$user->hasRole($requestedRole)) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke role ini.'
            ], 403);
        }

        // Set role aktif ke properti agar getFrontendConfig menggunakannya
        $user->active_role = $requestedRole;
        $config = $user->getFrontendConfig($requestedRole);

        // Tambahkan property role untuk memudahkan frontend (legacy compatibility)
        $user->role = $config['active_role'];

        return response()->json([
            'user' => $user,
            'config' => $config,
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Authentication"},
     *     summary="Logout user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="Successful logout"),
     *     @OA\Response(response="401", description="Unauthenticated"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function logout(Request $request)
    {
        try {
            if (!$request->user()) {
                return response()->json(['message' => 'Tidak terautentikasi.'], 401);
            }

            $request->user()->currentAccessToken()->delete();

            return response()->json(['message' => 'Berhasil logout.']);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat logout.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
