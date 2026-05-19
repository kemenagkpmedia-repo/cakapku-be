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
            $currentUser = $request->user();
            $query = User::with(['satker', 'roles']);

            // Jika role ADMIN atau OPERATOR, sembunyikan SUPER ADMIN dan ADMIN lain, serta batasi hanya pada Satker yang sama
            if ($currentUser->isActiveRole('ADMIN', $request) || $currentUser->isActiveRole('OPERATOR', $request)) {
                $query->whereDoesntHave('roles', function ($q) {
                    $q->whereIn('name', ['SUPER ADMIN', 'ADMIN']);
                });

                if ($currentUser->id_satker) {
                    $query->where('id_satker', $currentUser->id_satker);
                } else {
                    // Jika Admin/Operator tidak punya Satker, dia tidak bisa melihat siapa-siapa
                    $query->whereRaw('1 = 0');
                }
            }

            if ($request->filled('role')) {
                $query->role($request->input('role'));
            }

            $users = $query->get()->map(function ($user) {
                // Gunakan nama property yang tidak bentrok dengan relationship 'roles'
                $user->assigned_roles = $user->getRoleNames()->map(fn($r) => strtoupper($r));
                $user->role = $user->assigned_roles->first();
                return $user;
            });

            return response()->json($users);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data User.',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
    public function byRole(Request $request, $role)
    {
        try {
            $currentUser = request()->user();
            $query = User::with(['satker', 'roles'])->role($role);

            // Filter Satker jika ADMIN atau OPERATOR
            if ($currentUser->isActiveRole('ADMIN', $request) || $currentUser->isActiveRole('OPERATOR', $request)) {
                if ($currentUser->id_satker) {
                    $query->where('id_satker', $currentUser->id_satker);
                } else {
                    $query->whereRaw('1 = 0');
                }
            }

            $users = $query->get()->map(function ($user) {
                $user->assigned_roles = $user->getRoleNames()->map(fn($r) => strtoupper($r));
                $user->role = $user->assigned_roles->first();
                return $user;
            });

            return response()->json($users);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data User berdasarkan role.',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
            $currentUser = $request->user();
            $data = $request->validate([
                'nama' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users',
                'nip' => 'nullable|string|max:50',
                'email' => 'nullable|email|unique:users',
                'password' => 'required|string|min:6',
                'role' => 'nullable|string',
                'id_satker' => 'nullable|integer',
                'jabatan' => 'nullable|string',
                'gol_ruang' => 'nullable|string',
                'roles' => 'nullable|array',
                'role' => 'nullable|string',
            ]);

            $roles = $data['roles'] ?? ($data['role'] ?? ['USER']);
            if (is_string($roles))
                $roles = [$roles];

            // Hanya SUPER ADMIN yang boleh membuat user baru
            if (!$currentUser->isActiveRole('SUPER ADMIN', $request)) {
                return response()->json([
                    'message' => 'Anda tidak memiliki hak akses untuk menambah user baru.',
                ], 403);
            }
            unset($data['role'], $data['roles']);

            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);

            if (!empty($roles)) {
                $user->syncRoles($roles);
            }

            return response()->json($user, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal membuat User. Kemungkinan username atau email sudah digunakan.',
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
            $currentUser = $request->user();
            $targetUser = User::findOrFail($id);
            $data = $request->all();

            // Hierarki & Pembatasan Edit Satker
            if (!$currentUser->isActiveRole('SUPER ADMIN', $request)) {
                // Jika bukan Super Admin, dilarang merubah Satker
                if (isset($data['id_satker']) && $data['id_satker'] != $targetUser->id_satker) {
                    return response()->json([
                        'message' => 'Hanya Super Admin yang dapat merubah Satuan Kerja user.',
                    ], 403);
                }

                // ADMIN tidak boleh merubah SUPER ADMIN atau ADMIN lain
                if ($currentUser->isActiveRole('ADMIN', $request)) {
                    if ($targetUser->hasRole(['SUPER ADMIN', 'ADMIN'])) {
                        return response()->json([
                            'message' => 'Anda tidak memiliki hak untuk mengubah data Admin atau Super Admin.',
                        ], 403);
                    }
                }
            }

            // ADMIN juga tidak boleh memberikan role ADMIN/SUPER ADMIN ke user lain
            if ($currentUser->isActiveRole('ADMIN', $request)) {
                $requestedRoles = $data['roles'] ?? (isset($data['role']) ? [$data['role']] : []);
                foreach ($requestedRoles as $r) {
                    if (in_array(strtoupper($r), ['SUPER ADMIN', 'ADMIN'])) {
                        return response()->json([
                            'message' => 'Anda tidak memiliki hak untuk memberikan akses Admin atau Super Admin.',
                        ], 403);
                    }
                }
            }

            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            if (isset($data['roles'])) {
                $targetUser->syncRoles($data['roles']);
                unset($data['roles']);
            } elseif (isset($data['role'])) {
                $targetUser->syncRoles([$data['role']]);
                unset($data['role']);
            }

            $targetUser->update($data);

            return response()->json($targetUser);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User dengan ID ' . $id . ' tidak ditemukan.',
            ], 404);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal memperbarui User. Terjadi kesalahan database.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString() // Menambahkan trace untuk debug internal
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
            $currentUser = request()->user();
            $targetUser = User::findOrFail($id);

            // Hanya SUPER ADMIN yang boleh menghapus user
            if (!$currentUser->isActiveRole('SUPER ADMIN', request())) {
                return response()->json([
                    'message' => 'Anda tidak memiliki hak akses untuk menghapus user.',
                ], 403);
            }

            $targetUser->delete();

            return response()->json(['message' => 'User berhasil dihapus.']);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User dengan ID ' . $id . ' tidak ditemukan.',
            ], 404);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Gagal menghapus User. Mungkin masih ada data terkait.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
