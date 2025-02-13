<?php

namespace App\Http\Controllers;

use App\Models\Register;
use App\Models\RegisterCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegisterController extends Controller
{
    public function index(RegisterCategory $category)
    {
        return Inertia::render('Registers/Index', [
            'category' => $category,
            'registers' => $category->registers
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'register_category_id' => 'required|exists:register_categories,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'default' => 'boolean'
        ]);

        // Pokud je položka označena jako výchozí, zrušíme výchozí hodnotu u ostatních položek v kategorii
        if ($request->default) {
            Register::where('register_category_id', $request->register_category_id)
                   ->where('default', true)
                   ->update(['default' => false]);
        }

        $register = Register::create($validated);

        return redirect()->back()->with('message', 'Položka byla úspěšně vytvořena');
    }

    public function update(Request $request, Register $register)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'default' => 'boolean'
        ]);

        // Pokud je položka označena jako výchozí, zrušíme výchozí hodnotu u ostatních položek v kategorii
        if ($request->default) {
            Register::where('register_category_id', $register->register_category_id)
                   ->where('id', '!=', $register->id)
                   ->where('default', true)
                   ->update(['default' => false]);
        }

        $register->update($validated);

        return redirect()->back()->with('message', 'Položka byla úspěšně upravena');
    }

    public function destroy(Register $register)
    {
        $register->delete();

        return redirect()->back()->with('message', 'Položka byla úspěšně smazána');
    }
} 