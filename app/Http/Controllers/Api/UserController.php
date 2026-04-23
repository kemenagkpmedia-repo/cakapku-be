<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Get list of users",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="List of users")
     * )
     */
    public function index()
    {
        return response()->json(User::with('satker')->get());
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
     *     @OA\Response(response="201", description="User created successfully")
     * )
     */
    public function store(Request $request)
    {
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
        ]);

        $role = $data['role'] ?? 'USER';
        unset($data['role']);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        
        if ($role) {
            $user->assignRole($role);
        }

        return response()->json($user, 201);
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
     *     @OA\Response(response="200", description="User updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
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
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     tags={"Users"},
     *     summary="Delete user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="User deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
