<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name'),
                'created_at' => $user->created_at->format('d.m.Y'),
                'is_admin' => $user->hasRole(['admin', 'super-admin'])
            ];
        });

        return Inertia::render('Admin/User/Index', [
            'users' => $users
        ]);
    }

    public function create()
    {
        $roles = Role::all()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name
            ];
        });

        return Inertia::render('Admin/User/Create', [
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'roles' => 'array'
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

        return redirect()->route('admin.users.index')
            ->with('success', 'Uživatel byl úspěšně vytvořen.');
    }

    public function edit(User $user)
    {
        $roles = Role::all()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name
            ];
        });

        return Inertia::render('Admin/User/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('id'),
                'is_admin' => $user->hasRole(['admin', 'super-admin'])
            ],
            'roles' => $roles
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'roles' => 'array'
        ]);

        // Ochrana proti změně super-admin uživatele
        if ($user->hasRole('super-admin') && 
            (!Auth::user()?->hasRole('super-admin') || Auth::id() !== $user->id)) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Nemáte oprávnění upravovat super-admin uživatele.');
        }

        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
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

    public function destroy(User $user)
    {
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