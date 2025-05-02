<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;

class CollectionController extends Controller
{
    /**
     * Zobrazí hlavní stránku správy sbírek.
     */
    public function index(): Response
    {
        // Autorizace: Může uživatel zobrazit sbírky?
        if (!Gate::allows('collections.view')) {
            abort(403);
        }
        
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            // Tato kontrola je teoreticky zbytečná kvůli middleware, ale pro jistotu
            abort(403, 'User not authenticated.'); 
        }

        return Inertia::render('Collections/Index', [
            'collections' => $user->collections()->get(),
            // Předáme i oprávnění do frontendu
            'can' => [
                'create_collection' => Gate::allows('collections.create'),
                // Zde přidáme další oprávnění podle potřeby pro UI
            ]
        ]);
    }

    // Zde budou další metody pro CRUD operace se sbírkami...
}
