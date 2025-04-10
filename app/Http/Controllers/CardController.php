<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Card;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class CardController extends Controller
{
    

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 30); // Výchozí hodnota 30 karet na stránku
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $setId = $request->get('set_id', '');
        $type = $request->get('type', '');
        $rarity = $request->get('rarity', '');
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');

        // Validace počtu karet na stránku
        if (!in_array($perPage, [30, 60, 120])) {
            $perPage = 30;
        }

        // Cache key pro stránkování a filtry
        $cacheKey = "cards_page_{$page}_per_page_{$perPage}_search_{$search}_set_{$setId}_type_{$type}_rarity_{$rarity}_sort_{$sortBy}_{$sortDirection}";

        $result = Cache::remember($cacheKey, 3600, function () use ($perPage, $page, $search, $setId, $type, $rarity, $sortBy, $sortDirection) {
            $query = Card::select(['id', 'set_id', 'name', 'number', 'supertype', 'types', 'rarity', 'hp'])
                ->with(['set:id,name,series']); // Eager loading pro set

            // Aplikace filtrů
            if ($search) {
                $query->where('name', 'like', "%{$search}%");
            }
            if ($setId) {
                $query->where('set_id', $setId);
            }
            if ($type) {
                $query->where('supertype', $type);
            }
            if ($rarity) {
                $query->where('rarity', $rarity);
            }

            // Aplikace řazení
            $query->orderBy($sortBy, $sortDirection);

            return $query->paginate($perPage);
        });

        return inertia('Card/Index', [
            'cards' => $result,
            'filters' => [
                'search' => $search,
                'set_id' => $setId,
                'type' => $type,
                'rarity' => $rarity,
                'sort_by' => $sortBy,
                'sort_direction' => $sortDirection,
                'per_page' => $perPage
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Card $card)
    {
        // Načtení karty s eager loadingem pro související data
        $card->load([
            'set:id,name,series,release_date',
            'attacks:id,card_id,name,cost,damage,text',
            'variants:id,card_id,variant_normal,variant_holo,variant_reverse,variant_promo'
        ]);

        return inertia('Card/Show', [
            'card' => $card
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
