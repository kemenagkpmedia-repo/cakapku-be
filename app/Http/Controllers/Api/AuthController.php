<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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
                'password'   => 'required|string',
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

            return response()->json([
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'user'         => $user,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada database.',
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
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
