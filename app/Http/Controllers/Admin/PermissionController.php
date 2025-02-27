<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all()->map(function ($permission) {
            // Rozdělení názvu oprávnění na modul a akci
            $parts = explode('.', $permission->name);
            $module = $parts[0] ?? '';
            $action = $parts[1] ?? '';
            
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'module' => $module,
                'action' => $action,
                'roles_count' => $permission->roles()->count(),
                'created_at' => $permission->created_at,
            ];
        });

        // Seskupení oprávnění podle modulu
        $groupedPermissions = $permissions->groupBy('module');

        return Inertia::render('Admin/Permission/Index', [
            'permissions' => $permissions,
            'groupedPermissions' => $groupedPermissions
        ]);
    }

    public function create()
    {
        // Získání existujících modulů pro dropdown
        $modules = Permission::all()
            ->map(function ($permission) {
                $parts = explode('.', $permission->name);
                return $parts[0] ?? '';
            })
            ->unique()
            ->values()
            ->toArray();

        return Inertia::render('Admin/Permission/Create', [
            'modules' => $modules
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'module' => 'required|string|max:255',
            'action' => 'required|string|max:255',
        ]);

        $permissionName = $request->module . '.' . $request->action;

        // Kontrola, zda oprávnění již existuje
        if (Permission::where('name', $permissionName)->exists()) {
            return back()->withErrors([
                'module' => 'Toto oprávnění již existuje.'
            ]);
        }

        Permission::create(['name' => $permissionName]);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Oprávnění bylo úspěšně vytvořeno.');
    }

    public function edit(Permission $permission)
    {
        $parts = explode('.', $permission->name);
        
        // Získání existujících modulů pro dropdown
        $modules = Permission::all()
            ->map(function ($permission) {
                $parts = explode('.', $permission->name);
                return $parts[0] ?? '';
            })
            ->unique()
            ->values()
            ->toArray();

        return Inertia::render('Admin/Permission/Edit', [
            'permission' => [
                'id' => $permission->id,
                'name' => $permission->name,
                'module' => $parts[0] ?? '',
                'action' => $parts[1] ?? '',
                'roles_count' => $permission->roles()->count()
            ],
            'modules' => $modules
        ]);
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'module' => 'required|string|max:255',
            'action' => 'required|string|max:255',
        ]);

        $newPermissionName = $request->module . '.' . $request->action;

        // Kontrola, zda nové jméno již neexistuje (kromě aktuálního oprávnění)
        if ($newPermissionName !== $permission->name && 
            Permission::where('name', $newPermissionName)->exists()) {
            return back()->withErrors([
                'module' => 'Toto oprávnění již existuje.'
            ]);
        }

        $permission->name = $newPermissionName;
        $permission->save();

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Oprávnění bylo úspěšně aktualizováno.');
    }

    public function destroy(Permission $permission)
    {
        // Kontrola, zda je oprávnění používáno rolemi
        if ($permission->roles()->count() > 0) {
            return redirect()->route('admin.permissions.index')
                ->with('error', 'Toto oprávnění nelze smazat, protože je přiřazeno k rolím.');
        }

        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Oprávnění bylo úspěšně smazáno.');
    }
}