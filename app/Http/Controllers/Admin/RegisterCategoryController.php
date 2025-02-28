<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegisterCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegisterCategoryController extends Controller
{
    public function index()
    {
        $categories = RegisterCategory::withCount('registers')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'type' => $category->type,
                    'registers_count' => $category->registers_count
                ];
            });

        return Inertia::render('Admin/Register/Index', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50'
        ]);

        RegisterCategory::create($request->all());

        return redirect()->back()
            ->with('success', 'Kategorie číselníku byla úspěšně vytvořena.');
    }

    public function update(Request $request, RegisterCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50'
        ]);

        $category->update($request->all());

        return redirect()->back()
            ->with('success', 'Kategorie číselníku byla úspěšně aktualizována.');
    }

    public function destroy(RegisterCategory $category)
    {
        if ($category->registers()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Nelze smazat kategorii, která obsahuje položky.');
        }

        $category->delete();

        return redirect()->back()
            ->with('success', 'Kategorie číselníku byla úspěšně smazána.');
    }
} 