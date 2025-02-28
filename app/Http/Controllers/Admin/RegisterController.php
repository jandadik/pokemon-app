<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Register;
use App\Models\RegisterCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegisterController extends Controller
{
    public function index(RegisterCategory $category)
    {
        $registers = $category->registers()
            ->get()
            ->map(function ($register) {
                return [
                    'id' => $register->id,
                    'name' => $register->name,
                    'type' => $register->type,
                    'default' => $register->default
                ];
            });

        return Inertia::render('Admin/Register/Items', [
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'type' => $category->type
            ],
            'registers' => $registers
        ]);
    }

    public function store(Request $request, RegisterCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'default' => 'boolean'
        ]);

        // Pokud je položka označena jako výchozí, zrušíme výchozí hodnotu u ostatních položek v této kategorii
        if ($request->default) {
            $category->registers()->where('default', true)->update(['default' => false]);
        }

        $category->registers()->create($request->all());

        return redirect()->back()
            ->with('success', 'Položka číselníku byla úspěšně vytvořena.');
    }

    public function update(Request $request, RegisterCategory $category, Register $register)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'default' => 'boolean'
        ]);

        // Pokud je položka označena jako výchozí, zrušíme výchozí hodnotu u ostatních položek v této kategorii
        if ($request->default) {
            $category->registers()
                ->where('id', '!=', $register->id)
                ->where('default', true)
                ->update(['default' => false]);
        }

        $register->update($request->all());

        return redirect()->back()
            ->with('success', 'Položka číselníku byla úspěšně aktualizována.');
    }

    public function destroy(RegisterCategory $category, Register $register)
    {
        $register->delete();

        return redirect()->back()
            ->with('success', 'Položka číselníku byla úspěšně smazána.');
    }
} 