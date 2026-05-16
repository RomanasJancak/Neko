<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

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

    public function manage()
    {
        if (!auth()->user()->isAdminOrSuperAdmin()) {
            abort(403);
        }
        $roles = Role::withCount('users')->orderBy('name')->get();
        return view('role.manage', compact('roles'));
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
    public function store(Request $request)
    {
        if (!auth()->user()->isAdminOrSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:125', 'unique:roles,name'],
        ]);
        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        return response()->json(['message' => 'created', 'role' => $role]);
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
    public function update(Request $request, Role $role)
    {
        if (!auth()->user()->isAdminOrSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:125', Rule::unique('roles', 'name')->ignore($role->id)],
        ]);
        $role->update(['name' => $validated['name']]);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        return response()->json(['message' => 'updated', 'role' => $role]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if (!auth()->user()->isAdminOrSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        if ($role->users()->count() > 0) {
            return response()->json(['message' => 'Cannot delete a role that has users assigned to it.'], 422);
        }
        $role->delete();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        return response()->json(['message' => 'deleted']);
    }

    public function hierarchy()
    {
        if (!auth()->user()->isAdminOrSuperAdmin()) {
            abort(403);
        }
        $roles = Role::orderBy('name')->get(['id', 'name']);
        $links = DB::table('role_hierarchy')
            ->join('roles as parent', 'parent.id', '=', 'role_hierarchy.parent_role_id')
            ->join('roles as child',  'child.id',  '=', 'role_hierarchy.child_role_id')
            ->select(
                'role_hierarchy.parent_role_id',
                'role_hierarchy.child_role_id',
                'parent.name as parent_name',
                'child.name  as child_name'
            )
            ->orderBy('parent.name')
            ->orderBy('child.name')
            ->get();

        return view('role.hierarchy', compact('roles', 'links'));
    }

    public function storeHierarchy(Request $request)
    {
        if (!auth()->user()->isAdminOrSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        $validated = $request->validate([
            'parent_role_id' => ['required', 'integer', 'exists:roles,id'],
            'child_role_id'  => ['required', 'integer', 'exists:roles,id', 'different:parent_role_id'],
        ]);

        // Prevent duplicate
        $exists = DB::table('role_hierarchy')
            ->where('parent_role_id', $validated['parent_role_id'])
            ->where('child_role_id',  $validated['child_role_id'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'This relationship already exists.'], 422);
        }

        DB::table('role_hierarchy')->insert($validated);

        return response()->json(['message' => 'created']);
    }

    public function destroyHierarchy($parent, $child)
    {
        if (!auth()->user()->isAdminOrSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        $deleted = DB::table('role_hierarchy')
            ->where('parent_role_id', $parent)
            ->where('child_role_id',  $child)
            ->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Relationship not found.'], 404);
        }

        return response()->json(['message' => 'deleted']);
    }
}
