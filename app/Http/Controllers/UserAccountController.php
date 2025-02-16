<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserAccountController extends Controller
{
    public function create()
    {
        return Inertia::render('UserAccount/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Automatické přihlášení po registraci
        Auth::login($user);
        $user->assignRole('user');
        // Regenerace session
        $request->session()->regenerate();

        return redirect()->intended(route('index'))
            ->with('success', 'Účet byl úspěšně vytvořen.');
    }
}
