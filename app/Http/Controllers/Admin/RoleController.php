<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Controller pro správu uživatelských rolí
 * Zajišťuje CRUD operace nad rolemi a jejich oprávněními
 * 
 * @package App\Http\Controllers\Admin
 * @todo Vytvořit Form Request třídy pro validaci (StoreRoleRequest, UpdateRoleRequest)
 * @todo Přidat Resource třídu pro transformaci dat (RoleResource)
 * @security Implementovat middleware pro kontrolu oprávnění
 * @performance Optimalizovat načítání rolí a jejich oprávnění pomocí eager loading
 * @feature Přidat možnost duplikování role včetně oprávnění
 */
class RoleController extends Controller
{
    /**
     * Zobrazí seznam všech rolí
     * 
     * @return \Inertia\Response
     */
    public function index()
    {
        // TODO: Implementovat stránkování pro seznam rolí
        // TODO: Přidat vyhledávání podle názvu role
        // PERFORMANCE: Optimalizovat načítání počtu uživatelů v rolích pomocí eager loading
        
        $roles = Role::with('permissions')
            ->withCount('users')
            ->get();

        return Inertia::render('Admin/Role/Index', [
            'roles' => $roles
        ]);
    }

    /**
     * Zobrazí formulář pro vytvoření nové role
     * 
     * @return \Inertia\Response
     */
    public function create()
    {
        // TODO: Přidat možnost výběru výchozích oprávnění
        // TODO: Implementovat seskupení oprávnění podle modulů
        
        $permissions = Permission::all();

        return Inertia::render('Admin/Role/Create', [
            'permissions' => $permissions
        ]);
    }

    /**
     * Uloží novou roli
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // TODO: Vytvořit FormRequest pro validaci
        // SECURITY: Přidat validaci duplicitních oprávnění
        // HACK: Dočasné řešení validace, přesunout do FormRequestu
        
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

    /**
     * Zobrazí formulář pro úpravu role
     * 
     * @param Role $role
     * @return \Inertia\Response
     */
    public function edit(Role $role)
    {
        // TODO: Přidat zobrazení historie změn role
        // TODO: Zobrazit seznam uživatelů v roli
        
        $role->load('permissions');
        $permissions = Permission::all();

        return Inertia::render('Admin/Role/Edit', [
            'role' => $role,
            'permissions' => $permissions
        ]);
    }

    /**
     * Aktualizuje roli
     * 
     * @param Request $request
     * @param Role $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Role $role)
    {
        // TODO: Vytvořit UpdateRoleRequest pro validaci
        // SECURITY: Implementovat kontrolu pro systémové role
        // PERFORMANCE: Optimalizovat aktualizaci oprávnění pomocí transakce
        
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        if (!in_array($role->name, ['super-admin', 'admin'])) {
            $role->name = $request->name;
            $role->save();
        }

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

    /**
     * Odstraní roli
     * 
     * @param Role $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Role $role)
    {
        // FIXME: Ošetřit smazání role s přiřazenými uživateli
        // SECURITY: Přidat kontrolu závislostí před smazáním
        
        if (in_array($role->name, ['super-admin', 'admin'])) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Tuto roli nelze smazat.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role byla úspěšně smazána.');
    }
} 