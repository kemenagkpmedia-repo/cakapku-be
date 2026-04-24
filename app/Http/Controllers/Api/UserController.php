<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;

class UserController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Get list of users (optional filter by role)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         required=false,
     *         description="Filter users by role name (e.g. USER, ADMIN, OPERATOR, PIMPINAN)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="List of users"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function index(Request $request)
    {
        try {
            $query = User::with(['satker', 'roles']);

            if ($request->filled('role')) {
                $query->role($request->input('role'));
            }

            $users = $query->get()->map(function ($user) {
                $user->role = $user->getRoleNames()->first() ?? null;
                return $user;
            });

            return response()->json($users);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data User.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/users/by-role/{role}",
     *     tags={"Users"},
     *     summary="Get users by role name",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         required=true,
     *         description="Role name: USER | ADMIN | OPERATOR | PIMPINAN",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="List of users with given role"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function byRole($role)
    {
        try {
            $users = User::with(['satker', 'roles'])
                ->role($role)
                ->get()
                ->map(function ($user) {
                    $user->role = $user->getRoleNames()->first() ?? null;
                    return $user;
                });

            return response()->json($users);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data User berdasarkan role.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Create new user",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nama","nip"},
     *             @OA\Property(property="nama", type="string"),
     *             @OA\Property(property="nip", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="role", type="string", description="ADMIN/OPERATOR/USER/PIMPINAN"),
     *             @OA\Property(property="id_satker", type="integer")
     *         )
     *     ),
     *     @OA\Response(response="201", description="User created successfully"),
     *     @OA\Response(response="422", description="Validation error"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nama'      => 'required|string|max:255',
                'username'  => 'required|string|max:255|unique:users',
                'nip'       => 'nullable|string|max:50',
                'email'     => 'nullable|email|unique:users',
                'password'  => 'required|string|min:6',
                'role'      => 'nullable|string',
                'id_satker' => 'nullable|integer',
                'jabatan'   => 'nullable|string',
                'gol_ruang' => 'nullable|string',
            ]);

            $role = $data['role'] ?? 'USER';
            unset($data['role']);

            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);

            if ($role) {
                $user->assignRole($role);
            }

            return response()->json($user, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal membuat User. Kemungkinan username atau email sudah digunakan.',
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
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Update user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama", type="string"),
     *             @OA\Property(property="nip", type="string"),
     *             @OA\Property(property="role", type="string")
     *         )
     *     ),
     *     @OA\Response(response="200", description="User updated successfully"),
     *     @OA\Response(response="404", description="User not found"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $data = $request->all();

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            if (isset($data['role'])) {
                $user->syncRoles([$data['role']]);
                unset($data['role']);
            }

            $user->update($data);

            return response()->json($user);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User dengan ID ' . $id . ' tidak ditemukan.',
            ], 404);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal memperbarui User.',
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
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Delete user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="User deleted successfully"),
     *     @OA\Response(response="404", description="User not found"),
     *     @OA\Response(response="500", description="Server error")
     * )
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json(['message' => 'User berhasil dihapus.']);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User dengan ID ' . $id . ' tidak ditemukan.',
            ], 404);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal menghapus User. Mungkin masih ada data terkait.',
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
