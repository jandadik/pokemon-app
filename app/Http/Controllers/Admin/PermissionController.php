<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

/**
 * Controller pro správu oprávnění v systému
 * Zajišťuje CRUD operace nad oprávněními a jejich organizaci do modulů
 * 
 * @package App\Http\Controllers\Admin
 */
class PermissionController extends Controller
{
    // TODO: Vytvořit Form Request třídy pro validaci (StorePermissionRequest, UpdatePermissionRequest)
    // TODO: Přidat Resource třídu pro transformaci dat (PermissionResource)
    // SECURITY: Implementovat middleware pro kontrolu přístupu k operacím
    // PERFORMANCE: Optimalizovat načítání oprávnění a jejich vztahů
    // HACK: Dočasně používáme přímé mapování dat místo Resource třídy

    /**
     * Zobrazí seznam všech oprávnění
     * 
     * @return \Inertia\Response
     */
    public function index()
    {
        // TODO: Implementovat stránkování seznamu oprávnění
        // TODO: Přidat filtrování podle modulu
        // PERFORMANCE: Optimalizovat dotazy na počet rolí pomocí withCount

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

    /**
     * Zobrazí formulář pro vytvoření nového oprávnění
     * 
     * @return \Inertia\Response
     */
    public function create()
    {
        // TODO: Přidat validaci existujících modulů
        // TODO: Implementovat našeptávání pro název akce
        // NOTE: Zvážit cachování seznamu modulů pro lepší výkon

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

    /**
     * Uloží nové oprávnění
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // TODO: Přesunout validaci do StorePermissionRequest
        // TODO: Přidat validaci formátu modulu a akce
        // SECURITY: Implementovat kontrolu duplicit case-insensitive

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

    /**
     * Zobrazí formulář pro úpravu oprávnění
     * 
     * @param Permission $permission
     * @return \Inertia\Response
     */
    public function edit(Permission $permission)
    {
        // TODO: Přidat zobrazení rolí používajících toto oprávnění
        // TODO: Implementovat historii změn oprávnění
        // NOTE: Zvážit přidání preview dopadu změn na existující role

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

    /**
     * Aktualizuje oprávnění
     * 
     * @param Request $request
     * @param Permission $permission
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Permission $permission)
    {
        // TODO: Přesunout validaci do UpdatePermissionRequest
        // TODO: Přidat logování změn oprávnění
        // SECURITY: Implementovat kontrolu pro systémová oprávnění
        // PERFORMANCE: Optimalizovat aktualizaci pomocí transakce

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

    /**
     * Odstraní oprávnění
     * 
     * @param Permission $permission
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Permission $permission)
    {
        // FIXME: Ošetřit smazání oprávnění používaného v rolích
        // SECURITY: Přidat kontrolu závislostí před smazáním
        // TODO: Přidat možnost přesunu rolí na jiné oprávnění před smazáním

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