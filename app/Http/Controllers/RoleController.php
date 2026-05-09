<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->orderBy('name')->get();

        return view('role.index', compact('users'));
    }

    public function permissionsMatrix()
    {
        if (!auth()->user()->can('permission-view')) {
            abort(403, 'You do not have permission to view the permissions matrix.');
        }

        $roles = Role::with('permissions')->get();
        $permissions = Permission::orderBy('name')->get();

        $user = auth()->user();

        if ($user->isAdminOrSuperAdmin()) {
            $editablePermissionIds = $permissions->pluck('id')->toArray();
        } elseif ($user->can('permission-edit')) {
            $editablePermissionIds = $user->getAllPermissions()->pluck('id')->toArray();
        } else {
            $editablePermissionIds = [];
        }

        return view('role.permissions_matrix', compact('roles', 'permissions', 'editablePermissionIds'));
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $user = auth()->user();

        if (!$user->isAdminOrSuperAdmin() && !$user->can('permission-edit')) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'permissions'   => ['present', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $submittedIds = collect($validated['permissions']);

        // Determine which permission IDs this user may modify.
        if ($user->isAdminOrSuperAdmin()) {
            $editableIds = Permission::pluck('id');
        } else {
            $editableIds = $user->getAllPermissions()->pluck('id');
        }

        // Reject any submitted permission the user cannot assign (privilege escalation guard).
        $illegal = $submittedIds->diff($editableIds);
        if ($illegal->isNotEmpty()) {
            return response()->json([
                'message' => 'You cannot assign permissions you do not have.',
            ], 403);
        }

        // Preserve permissions on the role that this user cannot see/edit.
        $currentIds      = $role->permissions()->pluck('permissions.id');
        $nonEditableKept = $currentIds->diff($editableIds);

        $finalIds = $nonEditableKept->merge($submittedIds)->unique()->values()->toArray();

        $role->syncPermissions($finalIds);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json(['message' => 'Permissions updated.']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        //
    }
}
