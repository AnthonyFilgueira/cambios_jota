<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('permissions')->orderBy('name')->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function show(Role $role)
    {
        $modules = Module::orderBy('order')->get()->map(function ($module) use ($role) {
            $module->perms = Permission::where('module_id', $module->id)->orderBy('label')->get()->map(function ($perm) use ($role) {
                $perm->assigned = $role->hasPermissionTo($perm->name);
                return $perm;
            });
            return $module;
        });

        $allRoles = Role::orderBy('name')->get();

        return view('admin.roles.show', compact('role', 'modules', 'allRoles'));
    }

    public function togglePermission(Request $request, Role $role)
    {
        $request->validate([
            'permission' => 'required|string|exists:permissions,name',
        ]);

        $permName = $request->permission;

        if ($role->hasPermissionTo($permName)) {
            $role->revokePermissionTo($permName);
            $granted = false;
        } else {
            $role->givePermissionTo($permName);
            $granted = true;
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'granted' => $granted,
            'message' => $granted ? "Permiso '{$permName}' otorgado" : "Permiso '{$permName}' revocado",
        ]);
    }

    public function assignRoleToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role'    => 'required|exists:roles,name',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        $user->syncRoles([$request->role]);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json(['success' => true, 'message' => "Rol '{$request->role}' asignado a {$user->name}"]);
    }
}
