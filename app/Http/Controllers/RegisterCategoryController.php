<?php

namespace App\Http\Controllers;

use App\Models\RegisterCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegisterCategoryController extends Controller
{
    public function index()
    {
        $categories = RegisterCategory::with('registers')->get();
        
        return Inertia::render('Registers/Categories/Index', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255'
        ]);

        $category = RegisterCategory::create($validated);

        return redirect()->back()->with('message', 'Kategorie byla úspěšně vytvořena');
    }

    public function update(Request $request, RegisterCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255'
        ]);

        $category->update($validated);

        return redirect()->back()->with('message', 'Kategorie byla úspěšně upravena');
    }

    public function destroy(RegisterCategory $category)
    {
        $category->delete();

        return redirect()->back()->with('message', 'Kategorie byla úspěšně smazána');
    }
} 