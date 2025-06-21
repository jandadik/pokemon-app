<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Collection\StoreCollectionRequest;
use App\Http\Requests\Collection\UpdateCollectionRequest;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;
use App\Models\UserCollection;
use App\Services\CollectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Access\AuthorizationException;

class CollectionController extends Controller
{
    protected CollectionService $collectionService;

    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
        $this->authorizeResource(UserCollection::class, 'collection');

        // Aplikujeme middleware 'auth' a '2fa' na všechny metody kromě 'show'
        $this->middleware(['auth', '2fa'])->except('show');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ujistíme se, že uživatel je přihlášen (i když by to měl řešit middleware)
        // Tuto kontrolu by měl primárně řešit middleware 'auth'
        // if (!$user) {
        //     // V reálné aplikaci by zde mohlo být přesměrování na login nebo chyba 401/403
        //     return response()->json(['message' => 'Uživatel není přihlášen'], 401);
        // }

        $collections = $this->collectionService->getUserCollections($user);

        return Inertia::render('Collections/Index', [
            'collections' => $collections,
            'can' => [
                // Oprávnění pro vytváření nových kolekcí
                'create_collection' => $user ? $user->can('create', UserCollection::class) : false,
                // Obecná oprávnění pro kolekce - budou se kontrolovat na úrovni konkrétní kolekce v komponentě
                'update' => true,
                'delete' => true,
                'setDefault' => true,
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        // Autorizace pro 'create' je již řešena pomocí $this->authorizeResource v konstruktoru.
        // Metoda `can` v User modelu (nebo Gate::allows) by měla být také schopna ověřit 'create' 
        // pro UserCollection::class, pokud je policy správně nastavena.
        // 
        // Příklad explicitní kontroly, pokud by nebyla použita authorizeResource:
        // if (!Auth::user()->can('create', UserCollection::class)) {
        //     abort(403, 'Nemáte oprávnění vytvářet kolekce.');
        // }

        return Inertia::render('Collections/Create', [
            // Zde můžete předat jakákoliv data potřebná pro formulář,
            // např. výchozí hodnoty nebo seznamy pro select boxy.
            // Pro jednoduchý create formulář to nemusí být nutné.
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCollectionRequest $request): RedirectResponse
    {
        // Autorizace 'create' je již řešena pomocí $this->authorizeResource
        // a StoreCollectionRequest::authorize() by měla řešit obecné oprávnění uživatele vytvářet.
        // if (!Gate::allows('create', UserCollection::class)) {
        //     abort(403, 'Nemáte oprávnění ukládat kolekce.');
        // }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login'); 
        }

        // Validated data is now retrieved from the Form Request
        $dataToCreate = $request->validated();

        try {
            $collection = $this->collectionService->createCollection($user, $dataToCreate);

            return redirect()->route('collections.show', $collection->id)
                             ->with('success', 'Kolekce byla úspěšně vytvořena.');
        } catch (\Exception $e) {
            // Log::error("Chyba při vytváření kolekce: " . $e->getMessage()); // Příklad logování
            return back()->withInput()->with('error', 'Při vytváření kolekce došlo k chybě.'); // Obecná chybová hláška pro uživatele
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Request  $request
     * @param  UserCollection  $collection
     * @return \Inertia\Response|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request, UserCollection $collection)
    {
        // Validace filtrů (všechny jsou volitelné)
        $filters = $request->validate([
            'search' => 'sometimes|string|max:255',
            'condition' => 'sometimes|string|in:near_mint,excellent,good,played,poor',
            'language' => 'sometimes|string|in:english,czech,german,french,japanese',
            'rarity' => 'sometimes|string|max:100',
            'price_min' => 'sometimes|numeric|min:0',
            'price_max' => 'sometimes|numeric|min:0',
            'date_from' => 'sometimes|date',
            'date_to' => 'sometimes|date',
            'sort_by' => 'sometimes|string|in:name,number,rarity,condition,language,quantity,price,created_at',
            'sort_direction' => 'sometimes|string|in:asc,desc',
            'per_page' => 'sometimes|integer|in:2,10,30,60,100',
            'page' => 'sometimes|integer|min:1'
        ]);

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $perPage = $filters['per_page'] ?? 30;

        // Načtení položek kolekce s pagination a filtry
        $items = app(\App\Services\CollectionItemService::class)
            ->getItemsForCollectionPaginated($collection, $filters, $sortBy, $sortDirection, $perPage);

        // Načtení statistik kolekce
        $stats = app(\App\Services\CollectionItemService::class)
            ->getCollectionStats($collection);

        // Načtení možností pro filtry (dropdowny)
        $filterOptions = app(\App\Services\CollectionItemService::class)
            ->getFilterOptions($collection);

        // Oprávnění pro akce nad položkami
        $user = $request->user();
        $can = [
            'edit' => \Illuminate\Support\Facades\Gate::allows('update', $collection),
            'delete' => \Illuminate\Support\Facades\Gate::allows('delete', $collection),
            'toggleDefault' => \Illuminate\Support\Facades\Gate::allows('setDefault', $collection),
            'toggleVisibility' => \Illuminate\Support\Facades\Gate::allows('update', $collection),
            'update' => $user ? $user->can('update', $collection) : false,
            'view' => $user ? $user->can('view', $collection) : false,
        ];

        // Předání všech potřebných dat do Inertia
        return Inertia::render('Collections/Show', [
            'collection' => $collection,
            'items' => $items, // paginovaná data
            'stats' => $stats, // statistiky
            'filterOptions' => $filterOptions, // možnosti pro dropdowny
            'filters' => $filters, // aktuální stav filtrů
            'can' => $can,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Request  $request
     * @param  UserCollection  $collection
     * @return \Inertia\Response|\Illuminate\Http\JsonResponse
     */
    public function edit(Request $request, UserCollection $collection)
    {
        // Autorizace 'update' (pro zobrazení edit formuláře) je již řešena pomocí $this->authorizeResource
        // if (!Gate::allows('update', $collection)) {
        //     abort(403, 'Nemáte oprávnění upravovat tuto kolekci.');
        // }

        return Inertia::render('Collections/Edit', [
            'collection' => $collection,
            // 'formMode' => 'edit' // Pokud bychom měli společnou komponentu CreateOrEdit
             'can' => [
                // Příklad oprávnění, které můžeme chtít předat do Vue komponenty
                'update_collection' => Gate::allows('update', $collection),
                'delete_collection' => Gate::allows('delete', $collection),
            ]
        ]);
        // return response()->json(['message' => "Endpoint pro zobrazení formuláře pro editaci kolekce ID: {$collection->id}"]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateCollectionRequest  $request  // Changed type hint
     * @param  UserCollection  $collection
     * @return RedirectResponse
     */
    public function update(UpdateCollectionRequest $request, UserCollection $collection): RedirectResponse // Changed type hint
    {
        // Autorizace 'update' je již řešena pomocí $this->authorizeResource
        // a UpdateCollectionRequest::authorize() by měla řešit, zda uživatel může *obecně* aktualizovat.
        // Policy metoda 'update' řeší, zda může aktualizovat *tuto konkrétní* kolekci.
        // if (!Gate::allows('update', $collection)) {
        //     abort(403, 'Nemáte oprávnění aktualizovat tuto kolekci.');
        // }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            // This should ideally not be reached if authorize() in FormRequest requires authentication
            return redirect()->route('login');
        }

        // Validated data is now retrieved from the Form Request
        $dataToUpdate = $request->validated();

        try {
            $this->collectionService->updateCollection($collection, $dataToUpdate, $user);

            return redirect()->route('collections.show', $collection->id)
                             ->with('success', 'Kolekce byla úspěšně aktualizována.');
        } catch (\Exception $e) {
            // Log::error("Chyba při aktualizaci kolekce ID {$collection->id}: " . $e->getMessage());
            return back()->withInput()->with('error', 'Při aktualizaci kolekce došlo k chybě.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @param  UserCollection  $collection
     * @return RedirectResponse
     */
    public function destroy(Request $request, UserCollection $collection): RedirectResponse
    {
        // Autorizace 'delete' je již řešena pomocí $this->authorizeResource
        // if (!Gate::allows('delete', $collection)) {
        //     abort(403, 'Nemáte oprávnění smazat tuto kolekci.');
        // }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        try {
            $this->collectionService->deleteCollection($collection, $user);

            return redirect()->route('collections.index')
                             ->with('success', 'Kolekce byla úspěšně smazána.');
        } catch (\Exception $e) {
            // Log::error("Chyba při mazání kolekce ID {$collection->id}: " . $e->getMessage());
            return redirect()->route('collections.show', $collection->id)
                             ->with('error', 'Při mazání kolekce došlo k chybě: ' . $e->getMessage());
        }
    }

    public function toggleDefault(Request $request, UserCollection $collection)
    {
        $this->authorize('update', $collection); // Použijeme existující 'update' oprávnění z policy

        try {
            // Pokud kolekce už je výchozí, necháme ji tak (toto by nemělo nastat, protože
            // v UI skrýváme tlačítko pro výchozí kolekce)
            if (!$collection->is_default) {
                $this->collectionService->setCollectionAsDefault($collection, Auth::user());
            }
            
            // Vrátíme se zpět s aktualizovanou kolekcí a zprávou
            return redirect()->back()->with('success', __('collections.messages.default_updated_successfully'));
        } catch (AuthorizationException $e) {
            // Chyba autorizace specificky
            // Log::error("Authorization error in toggleDefault for collection ID {$collection->id}: " . $e->getMessage());
            return redirect()->back()->with('error', __('collections.messages.toggle_default_unauthorized'));
        } catch (\Exception $e) {
            // Log::error("Chyba při přepínání výchozího stavu pro kolekci ID {$collection->id}: " . $e->getMessage());
            return redirect()->back()->with('error', __('collections.messages.toggle_default_failed'));
        }
    }

    public function toggleVisibility(Request $request, UserCollection $collection)
    {
        $this->authorize('update', $collection); // Použijeme existující 'update' oprávnění z policy

        try {
            // Přepneme viditelnost - pokud byla veřejná, nyní bude soukromá a naopak
            $newVisibility = !$collection->is_public;
            $this->collectionService->setCollectionVisibility($collection, Auth::user(), $newVisibility);

            // Vrátíme se zpět s aktualizovanou kolekcí a zprávou
            return redirect()->back()->with('success', __('collections.messages.visibility_updated_successfully'));
        } catch (\Exception $e) {
            // Log::error("Chyba při přepínání viditelnosti pro kolekci ID {$collection->id}: " . $e->getMessage());
            return redirect()->back()->with('error', __('collections.messages.toggle_visibility_failed'));
        }
    }

    // Zde budou další metody pro CRUD operace se sbírkami...
}
