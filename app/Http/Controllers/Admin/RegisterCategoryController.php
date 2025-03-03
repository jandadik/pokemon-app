<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegisterCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Controller pro správu kategorií číselníků
 * Zajišťuje CRUD operace nad kategoriemi číselníků a jejich organizaci
 */
class RegisterCategoryController extends Controller
{
    // TODO: Vytvořit Form Request třídy pro validaci (StoreCategoryRequest, UpdateCategoryRequest)
    // TODO: Přidat Resource třídu pro transformaci dat (RegisterCategoryResource)
    // SECURITY: Implementovat middleware pro kontrolu přístupu k operacím
    // PERFORMANCE: Optimalizovat načítání kategorií a jejich položek
    // NOTE: Kategorie jsou používány pro organizaci číselníkových hodnot v systému

    /**
     * Zobrazí seznam všech kategorií číselníků
     * 
     * @return \Inertia\Response
     */
    public function index()
    {
        // TODO: Implementovat stránkování seznamu kategorií
        // TODO: Přidat filtrování podle typu
        // PERFORMANCE: Optimalizovat načítání počtu položek pomocí withCount

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

    /**
     * Uloží novou kategorii číselníku
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // TODO: Přesunout validaci do StoreCategoryRequest
        // TODO: Přidat validaci unikátnosti kombinace name a type
        // SECURITY: Implementovat kontrolu duplicit case-insensitive

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50'
        ]);

        RegisterCategory::create($request->all());

        return redirect()->back()
            ->with('success', 'Kategorie číselníku byla úspěšně vytvořena.');
    }

    /**
     * Aktualizuje kategorii číselníku
     * 
     * @param Request $request
     * @param RegisterCategory $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, RegisterCategory $category)
    {
        // TODO: Přesunout validaci do UpdateCategoryRequest
        // TODO: Přidat logování změn kategorie
        // SECURITY: Implementovat kontrolu pro systémové kategorie
        // PERFORMANCE: Optimalizovat aktualizaci pomocí transakce

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50'
        ]);

        $category->update($request->all());

        return redirect()->back()
            ->with('success', 'Kategorie číselníku byla úspěšně aktualizována.');
    }

    /**
     * Odstraní kategorii číselníku
     * 
     * @param RegisterCategory $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(RegisterCategory $category)
    {
        // FIXME: Ošetřit smazání kategorie s přiřazenými položkami
        // SECURITY: Přidat kontrolu závislostí před smazáním
        // TODO: Přidat možnost přesunu položek do jiné kategorie před smazáním

        if ($category->registers()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Nelze smazat kategorii, která obsahuje položky.');
        }

        $category->delete();

        return redirect()->back()
            ->with('success', 'Kategorie číselníku byla úspěšně smazána.');
    }
} 