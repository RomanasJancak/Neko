<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Job;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;

use Illuminate\Http\Request;

use Illuminate\Pagination\LengthAwarePaginator;


use App\Models\Client;
use App\Models\Distance;
use App\Models\User;
use App\Models\Role;
use App\Models\Status;
use App\Models\PostalCode;
use App\Models\PackageType;
use App\Models\Package;
use App\Models\AddOn;
use App\Models\AddOnRule;
use App\Models\Task;
use App\Models\Day;
use App\Models\Pickuptask;
use App\Models\Returntask;
use App\Models\Customtask;
use App\Models\Note;
use App\Models\InvoiceItem;
use App\Models\Invoice;


use App\Services\BackupService;
use App\Services\SettingsService;

use App\Settings\UserSettingDefinition;

use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

use App\Models\JobTemplate;

use Illuminate\Support\Facades\Hash;
/**
 * @OA\Info(
 * title="Neko API Documentation",
 * version="1.0.0",
 * description="API endpoints for Neko Project User Management",
 * @OA\Contact(email="admin@neko.test")
 * )
 * * @OA\Server(
 * url=L5_SWAGGER_CONST_HOST,
 * description="Main API Server"
 * )
 * * @OA\SecurityScheme(
 * type="http",
 * scheme="bearer",
 * bearerFormat="JWT",
 * securityScheme="sanctum_auth"
 * )
 */
class UserController extends Controller
{
/**
     * @OA\Get(
     * path="/api/users",
     * summary="Get list of users",
     * tags={"Users"},
     * security={{"sanctum_auth": {}}},
     * @OA\Parameter(name="id", in="query", description="Filter by ID", required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="name", in="query", description="Filter by name", required=false, @OA\Schema(type="string")),
     * @OA\Parameter(name="email", in="query", description="Filter by email", required=false, @OA\Schema(type="string")),
     * @OA\Response(
     * response=200,
     * description="Successful response",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="boolean", example=true),
     * @OA\Property(property="data", type="object",
     * @OA\Property(property="data", type="array", @OA\Items(
     * @OA\Property(property="id", type="integer", example=1),
     * @OA\Property(property="name", type="string", example="John Doe"),
     * @OA\Property(property="email", type="string", example="john@example.com"),
     * @OA\Property(property="phone", type="string", example="+3706000000"),
     * @OA\Property(property="roles", type="array", @OA\Items(type="string", example="admin"))
     * ))
     * )
     * )
     * ),
     * @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($id = $request->get('id')) {
            $query->where('id', 'like', "%{$id}%");
        }
        if ($name = $request->get('name')) {
            $query->where('name', 'like', "%{$name}%");
        }
        if ($email = $request->get('email')) {
            $query->where('email', 'like', "%{$email}%");
        }

        $users = $query->paginate(10)->appends($request->query());

        // Using transform to keep response consistent with the OA spec
        $users->getCollection()->transform(function ($user) {
            return [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'roles' => $user->roles->pluck('name'),
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $users,
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/users",
     * summary="Create a new user",
     * tags={"Users"},
     * security={{"sanctum_auth": {}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name","email","password","role"},
     * @OA\Property(property="name", type="string", example="John Doe"),
     * @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     * @OA\Property(property="password", type="string", format="password", example="secret123"),
     * @OA\Property(property="password_confirmation", type="string", example="secret123"),
     * @OA\Property(property="role", type="integer", example=1)
     * )
     * ),
     * @OA\Response(response=201, description="User created successfully")
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'phone' => ['nullable', 'string', 'regex:/^\+?[1-9]\d{1,14}$/'],
            'role' => 'required|exists:roles,id',
        ]);

        try {
            $userData = collect($validatedData)->except('role')->toArray();
            $userData['password'] = Hash::make($validatedData['password']);
            
            $user = User::create($userData);
            $user->assignRole($validatedData['role']);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'user' => $user->load('roles'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     * path="/api/users/{user}",
     * summary="Update an existing user",
     * tags={"Users"},
     * security={{"sanctum_auth": {}}},
     * @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * @OA\JsonContent(
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="email", type="string"),
     * @OA\Property(property="password", type="string"),
     * @OA\Property(property="role", type="integer")
     * )
     * ),
     * @OA\Response(response=200, description="User updated successfully")
     * )
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'email'    => ['sometimes', 'required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'    => ['nullable', 'string', 'regex:/^\+?[1-9]\d{1,14}$/'],
            'password' => 'nullable|confirmed|min:6',
            'role'     => 'sometimes|exists:roles,id',
        ]);

        try {
            if (!empty($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            } else {
                unset($validatedData['password']);
            }

            $user->update(collect($validatedData)->except('role')->toArray());

            if (isset($validatedData['role'])) {
                $user->syncRoles([$validatedData['role']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'user'    => $user->load('roles'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     * path="/api/users/{user}",
     * summary="Delete a user",
     * tags={"Users"},
     * security={{"sanctum_auth": {}}},
     * @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Deleted"),
     * @OA\Response(response=409, description="Conflict - User has dependencies")
     * )
     */
    public function destroy(User $user)
    {
        try {
            $dependencies = [];
            // Check for relations before deleting
            foreach (['workloads', 'jobs', 'settings'] as $relation) {
                if ($user->$relation()->exists()) {
                    $dependencies[$relation] = $user->$relation->pluck('id');
                }
            }

            if (!empty($dependencies)) {
                return response()->json([
                    'success' => false,
                    'message' => 'User has dependencies and cannot be deleted.',
                    'dependencies' => $dependencies,
                ], 409);
            }

            $user->delete();
            return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/users/{user}",
     * summary="Get a specific user",
     * tags={"Users"},
     * security={{"sanctum_auth": {}}},
     * @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="User found")
     * )
     */
    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'user' => $user->load('roles'),
        ]);
    }
}
