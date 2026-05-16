<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isAdminOrSuperAdmin()) {
            abort(403);
        }
        $permissions = Permission::orderBy('name')->get();
        return view('permission.index', compact('permissions'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdminOrSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:125', 'unique:permissions,name'],
        ]);
        $permission = Permission::create(['name' => $validated['name'], 'guard_name' => 'web']);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        return response()->json(['message' => 'created', 'permission' => $permission]);
    }

    public function show(Permission $permission)
    {
        //
    }

    public function edit(Permission $permission)
    {
        //
    }

    public function update(Request $request, Permission $permission)
    {
        if (!auth()->user()->isAdminOrSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:125', Rule::unique('permissions', 'name')->ignore($permission->id)],
        ]);
        $permission->update(['name' => $validated['name']]);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        return response()->json(['message' => 'updated', 'permission' => $permission]);
    }

    public function destroy(Permission $permission)
    {
        if (!auth()->user()->isAdminOrSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        $permission->delete();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        return response()->json(['message' => 'deleted']);
    }
}
