<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')
            ->withCount('users')
            ->get();

        return Inertia::render('Admin/Role/Index', [
            'roles' => $roles
        ]);
    }

    public function create()
    {
        $permissions = Permission::all();

        return Inertia::render('Admin/Role/Create', [
            'permissions' => $permissions
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name]);
        
        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role byla úspěšně vytvořena.');
    }

    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::all();

        return Inertia::render('Admin/Role/Edit', [
            'role' => $role,
            'permissions' => $permissions
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        // Ochrana proti změně super-admin a admin rolí
        if (!in_array($role->name, ['super-admin', 'admin'])) {
            $role->name = $request->name;
            $role->save();
        }

        // Super-admin má vždy všechna oprávnění
        if ($role->name !== 'super-admin') {
            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions([]);
            }
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role byla úspěšně aktualizována.');
    }

    public function destroy(Role $role)
    {
        // Ochrana proti smazání důležitých rolí
        if (in_array($role->name, ['super-admin', 'admin'])) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Tuto roli nelze smazat.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role byla úspěšně smazána.');
    }
} 