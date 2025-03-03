<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

/**
 * Controller pro správu uživatelů v administraci
 * Zajišťuje kompletní správu uživatelských účtů, jejich rolí a oprávnění
 */
class UserController extends Controller
{
    // TODO: Vytvořit Form Request třídy pro validaci (StoreUserRequest, UpdateUserRequest)
    // TODO: Přidat Resource třídu pro transformaci dat (UserResource)
    // SECURITY: Implementovat middleware pro kontrolu přístupu k operacím
    // PERFORMANCE: Optimalizovat načítání uživatelů a jejich vztahů
    // NOTE: Správa uživatelů je klíčová pro bezpečnost systému

    /**
     * Zobrazí seznam všech uživatelů
     * 
     * @return \Inertia\Response
     */
    public function index()
    {
        // TODO: Implementovat stránkování seznamu uživatelů
        // TODO: Přidat filtrování podle rolí a stavu
        // PERFORMANCE: Optimalizovat načítání rolí a oprávnění pomocí eager loading

        $users = User::with(['roles', 'permissions'])->get()->map(function ($user) {
            // Seskupení oprávnění podle modulu
            $groupedPermissions = $user->getDirectPermissions()
                ->groupBy(function ($permission) {
                    return explode('.', $permission->name)[0];
                })
                ->map(function ($permissions) {
                    return $permissions->map(function ($permission) {
                        $parts = explode('.', $permission->name);
                        return [
                            'module' => $parts[0],
                            'action' => $parts[1],
                            'name' => $permission->name
                        ];
                    });
                });

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name'),
                'permissions' => $groupedPermissions,
                'created_at' => $user->created_at->format('d.m.Y'),
                'is_admin' => $user->hasRole(['admin', 'super-admin'])
            ];
        });

        return Inertia::render('Admin/User/Index', [
            'users' => $users
        ]);
    }

    /**
     * Zobrazí formulář pro vytvoření nového uživatele
     * 
     * @return \Inertia\Response
     */
    public function create()
    {
        // TODO: Implementovat načítání výchozích rolí
        // TODO: Přidat seskupení oprávnění podle modulů
        // PERFORMANCE: Optimalizovat načítání rolí a oprávnění

        $roles = Role::all()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name
            ];
        });

        $permissions = Permission::all()->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name
            ];
        });

        return Inertia::render('Admin/User/Create', [
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }

    /**
     * Uloží nového uživatele
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // TODO: Přesunout validaci do StoreUserRequest
        // TODO: Přidat validaci složitosti hesla
        // SECURITY: Implementovat rate limiting pro vytváření účtů
        // PERFORMANCE: Optimalizovat přiřazení rolí a oprávnění pomocí transakce

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'roles' => 'array',
            'permissions' => 'array'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->has('roles')) {
            $roles = Role::whereIn('id', $request->roles)->get();
            $user->syncRoles($roles);
        }

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $user->syncPermissions($permissions);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Uživatel byl úspěšně vytvořen.');
    }

    /**
     * Zobrazí formulář pro úpravu uživatele
     * 
     * @param User $user
     * @return \Inertia\Response
     */
    public function edit(User $user)
    {
        // TODO: Přidat zobrazení historie změn uživatele
        // TODO: Zobrazit statistiky přihlášení
        // SECURITY: Implementovat kontrolu přístupu k citlivým datům

        $roles = Role::all()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name
            ];
        });

        $permissions = Permission::all()->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name
            ];
        });

        return Inertia::render('Admin/User/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('id'),
                'permissions' => $user->permissions->pluck('id'),
                'is_admin' => $user->hasRole(['admin', 'super-admin'])
            ],
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }

    /**
     * Aktualizuje uživatele
     * 
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        // TODO: Přesunout validaci do UpdateUserRequest
        // TODO: Přidat logování změn uživatele
        // SECURITY: Implementovat kontrolu pro systémové uživatele
        // PERFORMANCE: Optimalizovat aktualizaci pomocí transakce

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'roles' => 'array',
            'permissions' => 'array'
        ]);

        // Ochrana proti změně super-admin uživatele
        if ($user->hasRole('super-admin') && 
            (!Auth::user()?->hasRole('super-admin') || Auth::id() !== $user->id)) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Nemáte oprávnění upravovat super-admin uživatele.');
        }

        // Aktualizace základních údajů
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        
        // Aktualizace hesla, pokud bylo zadáno
        if ($request->filled('password')) {
            // SECURITY: Zvážit přidání historie hesel pro prevenci opakování
            $request->validate([
                'password' => ['required', 'confirmed', Password::defaults()]
            ]);
            
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }
        
        $user->save();

        // Aktualizace rolí
        if ($request->has('roles')) {
            // Ochrana proti odebrání super-admin role
            if ($user->hasRole('super-admin') && !in_array(Role::where('name', 'super-admin')->first()->id, $request->roles)) {
                return redirect()->route('admin.users.edit', $user->id)
                    ->with('error', 'Nelze odebrat roli super-admin.');
            }
            
            $roles = Role::whereIn('id', $request->roles)->get();
            $user->syncRoles($roles);
            
            // Aktualizace přímých oprávnění
            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $user->syncPermissions($permissions);
            }
        } else {
            // Ochrana proti odebrání všech rolí super-admin uživateli
            if ($user->hasRole('super-admin')) {
                return redirect()->route('admin.users.edit', $user->id)
                    ->with('error', 'Nelze odebrat roli super-admin.');
            }
            
            $user->syncRoles([]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Uživatel byl úspěšně aktualizován.');
    }

    /**
     * Odstraní uživatele
     * 
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // FIXME: Ošetřit smazání uživatele s aktivními relacemi
        // SECURITY: Přidat kontrolu závislostí před smazáním
        // TODO: Přidat možnost deaktivace místo smazání
        // NOTE: Zvážit soft delete pro audit historii

        // Ochrana proti smazání vlastního účtu
        if (Auth::id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Nemůžete smazat svůj vlastní účet.');
        }

        // Ochrana proti smazání super-admin uživatele
        if ($user->hasRole('super-admin')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Nemůžete smazat super-admin uživatele.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Uživatel byl úspěšně smazán.');
    }
} 