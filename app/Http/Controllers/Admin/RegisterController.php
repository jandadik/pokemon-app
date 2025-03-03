<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Register;
use App\Models\RegisterCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Controller pro správu položek číselníků
 * Zajišťuje CRUD operace nad položkami číselníků v rámci jejich kategorií
 */
class RegisterController extends Controller
{
    // TODO: Vytvořit Form Request třídy pro validaci (StoreRegisterRequest, UpdateRegisterRequest)
    // TODO: Přidat Resource třídu pro transformaci dat (RegisterResource)
    // SECURITY: Implementovat middleware pro kontrolu přístupu k operacím
    // PERFORMANCE: Optimalizovat načítání položek a jejich vztahů
    // NOTE: Položky číselníků jsou vždy vázány na konkrétní kategorii

    /**
     * Zobrazí seznam položek pro danou kategorii číselníku
     * 
     * @param RegisterCategory $category
     * @return \Inertia\Response
     */
    public function index(RegisterCategory $category)
    {
        // TODO: Implementovat stránkování seznamu položek
        // TODO: Přidat filtrování podle typu a stavu
        // PERFORMANCE: Optimalizovat načítání dat pomocí eager loading

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

    /**
     * Uloží novou položku číselníku
     * 
     * @param Request $request
     * @param RegisterCategory $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, RegisterCategory $category)
    {
        // TODO: Přesunout validaci do StoreRegisterRequest
        // TODO: Přidat validaci unikátnosti v rámci kategorie
        // SECURITY: Implementovat kontrolu duplicit case-insensitive
        // PERFORMANCE: Optimalizovat nastavení výchozí hodnoty pomocí transakce

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'default' => 'boolean'
        ]);

        // Pokud je položka označena jako výchozí, zrušíme výchozí hodnotu u ostatních položek v této kategorii
        if ($request->default) {
            // TODO: Přesunout logiku nastavení výchozí hodnoty do service třídy
            // PERFORMANCE: Optimalizovat update pomocí jednoho dotazu
            $category->registers()->where('default', true)->update(['default' => false]);
        }

        $category->registers()->create($request->all());

        return redirect()->back()
            ->with('success', 'Položka číselníku byla úspěšně vytvořena.');
    }

    /**
     * Aktualizuje položku číselníku
     * 
     * @param Request $request
     * @param RegisterCategory $category
     * @param Register $register
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, RegisterCategory $category, Register $register)
    {
        // TODO: Přesunout validaci do UpdateRegisterRequest
        // TODO: Přidat logování změn položky
        // SECURITY: Implementovat kontrolu pro systémové položky
        // PERFORMANCE: Optimalizovat aktualizaci pomocí transakce

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'default' => 'boolean'
        ]);

        // Pokud je položka označena jako výchozí, zrušíme výchozí hodnotu u ostatních položek v této kategorii
        if ($request->default) {
            // TODO: Přesunout logiku nastavení výchozí hodnoty do service třídy
            // FIXME: Ošetřit race condition při současné změně více položek
            $category->registers()
                ->where('id', '!=', $register->id)
                ->where('default', true)
                ->update(['default' => false]);
        }

        $register->update($request->all());

        return redirect()->back()
            ->with('success', 'Položka číselníku byla úspěšně aktualizována.');
    }

    /**
     * Odstraní položku číselníku
     * 
     * @param RegisterCategory $category
     * @param Register $register
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(RegisterCategory $category, Register $register)
    {
        // FIXME: Ošetřit smazání výchozí položky
        // SECURITY: Přidat kontrolu závislostí před smazáním
        // TODO: Přidat možnost přesunu závislostí na jinou položku před smazáním

        // TODO: Implementovat kontrolu použití položky v systému
        // NOTE: Před smazáním je potřeba ověřit, zda položka není používána

        $register->delete();

        return redirect()->back()
            ->with('success', 'Položka číselníku byla úspěšně smazána.');
    }
} 