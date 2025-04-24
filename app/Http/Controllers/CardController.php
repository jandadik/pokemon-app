<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Card;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CardController extends Controller
{
    

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 30);
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $setId = $request->get('set_id', '');
        $type = $request->get('type', '');
        $rarity = $request->get('rarity', '');
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');

        // Validace vstupních parametrů
        if (!is_numeric($page) || $page < 1) {
            $page = 1;
        }

        if (!in_array($perPage, [30, 60, 120])) {
            $perPage = 30;
        }

        // Cache key pro stránkování a filtry
        $cacheKey = "cards_page_{$page}_per_page_{$perPage}_search_{$search}_set_{$setId}_type_{$type}_rarity_{$rarity}_sort_{$sortBy}_{$sortDirection}";

        // Pro řazení podle ceny musíme použít jiný přístup
        if ($sortBy === 'price') {
            Cache::forget($cacheKey);
            $result = $this->getCardsOrderedByPrice($perPage, $page, $search, $setId, $type, $rarity, $sortDirection);
        } else {
            $result = Cache::remember($cacheKey, 3600, function () use ($perPage, $page, $search, $setId, $type, $rarity, $sortBy, $sortDirection) {
                return $this->getCards($perPage, $page, $search, $setId, $type, $rarity, $sortBy, $sortDirection);
            });
        }

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
     * Získá karty seřazené podle ceny (speciální případ)
     */
    private function getCardsOrderedByPrice($perPage, $page, $search, $setId, $type, $rarity, $sortDirection)
    {
        \Log::info("Řazení podle ceny - vstupní parametry: page={$page}, perPage={$perPage}, sortDirection={$sortDirection}");
        
        $query = Card::select(['id', 'set_id', 'name', 'number', 'supertype', 'types', 'rarity', 'hp', 'img_file_small', 'img_small'])
            ->with(['set:id,name,series']);

        // Aplikace filtrů
        if ($search) {
            $query->whereRaw('MATCH(name, number) AGAINST(? IN BOOLEAN MODE)', [$search . '*']);
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

        // Získáme všechny karty bez paginace
        $allCards = $query->get();
        
        \Log::info("Řazení podle ceny - počet karet před načtením cen: " . count($allCards));
        
        // Načteme ceny pro všechny karty najednou
        $cardIds = $allCards->map(function($card) {
            return $card->set_id . '-' . $card->number;
        })->toArray();
        
        // Načtení nejnovějších cen pro všechny karty v jednom dotazu
        $prices = DB::table('prices_cm')
            ->whereIn('card_id', $cardIds)
            ->whereIn('updated_at', function($query) use ($cardIds) {
                $query->select(DB::raw('MAX(updated_at)'))
                    ->from('prices_cm')
                    ->whereIn('card_id', $cardIds)
                    ->groupBy('card_id');
            })
            ->get()
            ->keyBy('card_id');
        
        // Přiřazení cen ke kartám
        foreach ($allCards as $card) {
            $cardId = $card->set_id . '-' . $card->number;
            if (isset($prices[$cardId])) {
                $card->prices_cm = (object)[
                    'avg30' => $prices[$cardId]->avg30,
                    'avg7' => $prices[$cardId]->avg7,
                    'avg1' => $prices[$cardId]->avg1,
                    'reverse_holo_avg30' => $prices[$cardId]->reverse_holo_avg30,
                    'trend_price' => $prices[$cardId]->trend_price,
                    'updated_at' => $prices[$cardId]->updated_at,
                    'card_id' => $prices[$cardId]->card_id
                ];
                $card->price_value_for_sort = $prices[$cardId]->avg30;
            } else {
                $card->price_value_for_sort = null;
            }
        }
        
        // Řazení karet podle ceny
        $sortedCards = $allCards->sortBy([
            ['price_value_for_sort', $sortDirection === 'desc' ? 'desc' : 'asc'],
            ['name', 'asc']
        ])->values();
        
        // Pro sestupné řazení přesuneme karty bez ceny na konec
        if ($sortDirection === 'desc') {
            $withPrice = $sortedCards->filter(fn($card) => $card->price_value_for_sort !== null);
            $withoutPrice = $sortedCards->filter(fn($card) => $card->price_value_for_sort === null);
            $sortedCards = $withPrice->concat($withoutPrice)->values();
        }
        
        // Manuální paginace
        $total = $sortedCards->count();
        $offset = ($page - 1) * $perPage;
        
        if ($offset >= $total) {
            $offset = 0;
            $page = 1;
        }
        
        $paginatedCards = $sortedCards->slice($offset, $perPage);
        
        // Vytvoření paginatoru
        $cards = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedCards,
            $total,
            $perPage,
            $page,
            [
                'path' => url('/cards'),
                'query' => request()->except(['page'])
            ]
        );
        
        $cards->appends(array_merge(
            request()->except(['page']),
            ['sort_by' => 'price', 'sort_direction' => $sortDirection]
        ));
        
        return $cards;
    }

    /**
     * Získá karty se standardním řazením podle sloupců
     */
    private function getCards($perPage, $page, $search, $setId, $type, $rarity, $sortBy, $sortDirection)
    {
        $query = Card::select(['id', 'set_id', 'name', 'number', 'supertype', 'types', 'rarity', 'hp', 'img_file_small', 'img_small'])
            ->with(['set:id,name,series']);

        // Fulltextové vyhledávání
        if ($search) {
            // Použijeme fulltext index pro vyhledávání
            $query->whereRaw('MATCH(name, number) AGAINST(? IN BOOLEAN MODE)', [$search . '*']);
        }

        // Ostatní filtry
        if ($setId) {
            $query->where('set_id', $setId);
        }
        if ($type) {
            $query->where('supertype', $type);
        }
        if ($rarity) {
            $query->where('rarity', $rarity);
        }

        // Řazení
        $query->orderBy($sortBy, $sortDirection);
        
        // Stránkování
        $cards = $query->paginate($perPage);
        
        // Načítání cen pro karty
        $this->loadCardPrices($cards);
        
        return $cards;
    }

    private function loadCardPrices($cards)
    {
        // Pokud nejsou žádné karty, ukončíme metodu
        if ($cards->isEmpty()) {
            return;
        }
        
        // Vytvoříme cache key pro ceny
        $cacheKey = 'card_prices_' . md5(implode(',', $cards->pluck('id')->toArray()));
        
        // Pokusíme se získat ceny z cache
        $prices = Cache::remember($cacheKey, 300, function() use ($cards) {
            // Vytvoříme ID karet ve formátu set_id-number
            $cardIds = $cards->map(function($card) {
                return $card->set_id . '-' . $card->number;
            })->toArray();
            
            // Načteme nejnovější ceny pro všechny karty v jednom dotazu
            return DB::table('prices_cm')
                ->whereIn('card_id', $cardIds)
                ->whereIn('updated_at', function($query) use ($cardIds) {
                    $query->select(DB::raw('MAX(updated_at)'))
                        ->from('prices_cm')
                        ->whereIn('card_id', $cardIds)
                        ->groupBy('card_id');
                })
                ->get()
                ->keyBy('card_id');
        });
        
        // Přiřazení cen ke kartám
        foreach ($cards as $card) {
            $cardId = $card->set_id . '-' . $card->number;
            if (isset($prices[$cardId])) {
                $card->prices_cm = (object)[
                    'avg30' => $prices[$cardId]->avg30,
                    'avg7' => $prices[$cardId]->avg7,
                    'avg1' => $prices[$cardId]->avg1,
                    'reverse_holo_avg30' => $prices[$cardId]->reverse_holo_avg30,
                    'trend_price' => $prices[$cardId]->trend_price,
                    'updated_at' => $prices[$cardId]->updated_at,
                    'card_id' => $prices[$cardId]->card_id
                ];
            }
        }
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
