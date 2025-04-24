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
        $perPage = $request->get('per_page', 30); // Výchozí hodnota 30 karet na stránku
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $setId = $request->get('set_id', '');
        $type = $request->get('type', '');
        $rarity = $request->get('rarity', '');
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');

        // Pro správné fungování s pagination vždy zajistíme, že máme platnou hodnotu stránky
        if (!is_numeric($page) || $page < 1) {
            $page = 1;
        }

        // Validace počtu karet na stránku
        if (!in_array($perPage, [30, 60, 120])) {
            $perPage = 30;
        }

        // Debug: Vypsat informace o požadavku
        \Log::info('CardController@index - Request: ' . json_encode([
            'page' => $page,
            'per_page' => $perPage,
            'search' => $search,
            'set_id' => $setId,
            'type' => $type,
            'rarity' => $rarity,
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
            'url' => $request->fullUrl(),
            'previous_url' => url()->previous()
        ]));

        // Debug: Vypsat strukturu databáze
        \Log::info('Sloupce v tabulce cards: ' . implode(', ', Schema::getColumnListing('cards')));

        // Cache key pro stránkování a filtry
        $cacheKey = "cards_page_{$page}_per_page_{$perPage}_search_{$search}_set_{$setId}_type_{$type}_rarity_{$rarity}_sort_{$sortBy}_{$sortDirection}";

        // Pro řazení podle ceny musíme použít jiný přístup - nelze cachovat (nebo musíme cache zneplatnit)
        if ($sortBy === 'price') {
            // Zneplatníme cache pro tento požadavek, protože řazení podle ceny vyžaduje vždy aktuální data
            Cache::forget($cacheKey);
            
            $result = $this->getCardsOrderedByPrice($perPage, $page, $search, $setId, $type, $rarity, $sortDirection);
        } else {
            // Standardní cacheování pro ostatní typy řazení
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

        // Pro řazení podle ceny potřebujeme nejprve získat všechny karty bez paginace
        $allCards = $query->get();
        
        \Log::info("Řazení podle ceny - počet karet před načtením cen: " . count($allCards));
        
        // Načítání cen pro všechny karty
        foreach ($allCards as $card) {
            $cardId = $card->set_id . '-' . $card->number;
            
            $latestUpdate = DB::table('prices_cm')
                ->where('card_id', $cardId)
                ->select(DB::raw('MAX(updated_at) as latest_update'))
                ->first();
            
            if ($latestUpdate && $latestUpdate->latest_update) {
                $priceData = DB::table('prices_cm')
                    ->where('card_id', $cardId)
                    ->where('updated_at', $latestUpdate->latest_update)
                    ->first();
                
                if ($priceData) {
                    $card->prices_cm = (object)[
                        'avg30' => $priceData->avg30,
                        'avg7' => $priceData->avg7,
                        'avg1' => $priceData->avg1,
                        'reverse_holo_avg30' => $priceData->reverse_holo_avg30,
                        'trend_price' => $priceData->trend_price,
                        'updated_at' => $priceData->updated_at,
                        'card_id' => $priceData->card_id
                    ];
                }
            }
        }
        
        // Vždy inicializujeme price hodnotu, aby bylo řazení konzistentní
        foreach ($allCards as $card) {
            // Pokud karta nemá cenu, nastavíme ji na NULL
            if (!isset($card->prices_cm) || !isset($card->prices_cm->avg30)) {
                $card->price_value_for_sort = null;
            } else {
                $card->price_value_for_sort = $card->prices_cm->avg30;
            }
        }
        
        // Řazení všech karet podle ceny
        $sortedCards = $allCards->sortBy([
            ['price_value_for_sort', $sortDirection === 'desc' ? 'desc' : 'asc'],
            ['name', 'asc']  // Sekundární řazení podle jména pro konzistentní výsledky
        ])->values();
        
        // Pro sestupné řazení musíme karty bez ceny přesunout na konec
        if ($sortDirection === 'desc') {
            // Rozdělíme kolekci na karty s cenou a bez ceny
            $withPrice = $sortedCards->filter(function($card) {
                return $card->price_value_for_sort !== null;
            });
            
            $withoutPrice = $sortedCards->filter(function($card) {
                return $card->price_value_for_sort === null;
            });
            
            // Spojíme je zpět dohromady - karty s cenou před kartami bez ceny
            $sortedCards = $withPrice->concat($withoutPrice)->values();
        }
        
        \Log::info("Řazení podle ceny - počet karet po řazení: " . count($sortedCards));
        
        // Manuální paginace - získáme pouze část karet pro aktuální stránku
        $total = $sortedCards->count();
        $offset = ($page - 1) * $perPage;
        
        // Ošetření pro případ, kdy offset je mimo rozsah
        if ($offset >= $total) {
            $offset = 0;
            $page = 1;
        }
        
        $paginatedCards = $sortedCards->slice($offset, $perPage);
        
        \Log::info("Řazení podle ceny - paginace: offset={$offset}, count=" . $paginatedCards->count());
        
        // Pokus o alternativní řešení s manualním nastavením paginace
        // Vytvořím kompletní URL pro každou stránku, aby nedocházelo k problémům s relativními cestami
        $baseUrl = url('/cards'); // Absolutní URL
        $queryString = http_build_query(array_merge(
            request()->except(['page']), 
            ['sort_by' => 'price', 'sort_direction' => $sortDirection]
        ));
        
        $paginateOptions = [
            'path' => $baseUrl,
            'query' => request()->except(['page'])
        ];
        
        \Log::info("Řazení podle ceny - Absolutní URL pro paginaci: {$baseUrl}?{$queryString}");
        
        // Vytvoříme manuálně instanci LengthAwarePaginator pro správné zobrazení na frontendu
        $cards = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedCards,
            $total,
            $perPage,
            $page,
            $paginateOptions
        );
        
        // Explicitně nastavím všechny query parametry pro odkazy
        $cards->appends(array_merge(
            request()->except(['page']),
            ['sort_by' => 'price', 'sort_direction' => $sortDirection]
        ));
        
        // Zkontrolujeme vygenerované URL pro další stránky
        if ($cards->hasMorePages()) {
            \Log::info("Řazení podle ceny - URL pro další stránku: " . $cards->nextPageUrl());
        }
        
        return $cards;
    }

    /**
     * Získá karty se standardním řazením podle sloupců
     */
    private function getCards($perPage, $page, $search, $setId, $type, $rarity, $sortBy, $sortDirection)
    {
        $query = Card::select(['id', 'set_id', 'name', 'number', 'supertype', 'types', 'rarity', 'hp', 'img_file_small', 'img_small'])
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

        // Standardní řazení podle sloupců v databázi
        $query->orderBy($sortBy, $sortDirection);
        $cards = $query->paginate($perPage);
        
        // Načítání cen pro karty
        foreach ($cards as $card) {
            $cardId = $card->set_id . '-' . $card->number;
            
            $latestUpdate = DB::table('prices_cm')
                ->where('card_id', $cardId)
                ->select(DB::raw('MAX(updated_at) as latest_update'))
                ->first();
            
            if ($latestUpdate && $latestUpdate->latest_update) {
                $priceData = DB::table('prices_cm')
                    ->where('card_id', $cardId)
                    ->where('updated_at', $latestUpdate->latest_update)
                    ->first();
                
                if ($priceData) {
                    $card->prices_cm = (object)[
                        'avg30' => $priceData->avg30,
                        'avg7' => $priceData->avg7,
                        'avg1' => $priceData->avg1,
                        'reverse_holo_avg30' => $priceData->reverse_holo_avg30,
                        'trend_price' => $priceData->trend_price,
                        'updated_at' => $priceData->updated_at,
                        'card_id' => $priceData->card_id
                    ];
                }
            }
        }
        
        // Debug obrazků a existenci souborů
        foreach ($cards as $card) {
            // Debug: Logujeme všechny údaje o obrázcích
            \Log::info("Karta ID: {$card->id}, img_file_small: " . ($card->img_file_small ?: 'NULL') . 
                ", img_small: " . ($card->img_small ?: 'NULL'));
            
            if (!empty($card->img_file_small)) {
                // Cesta v databázi je ve formátu "card_images\cel25\1.png" (relativní bez /images/)
                // Převést obrácená lomítka na normální
                $normalizedPath = 'images/' . str_replace('\\', '/', $card->img_file_small);
                $fullPath = public_path($normalizedPath);
                
                \Log::info("Kontroluji cestu: {$normalizedPath}, Plná cesta: {$fullPath}");
                
                // Pouze pro případné logování - soubor neexistuje
                if (!file_exists($fullPath)) {
                    \Log::info("Soubor pro kartu ID {$card->id} neexistuje: {$normalizedPath} (plná cesta: {$fullPath})");
                } else {
                    \Log::info("Soubor pro kartu ID {$card->id} existuje: {$normalizedPath}");
                }
            }
        }
        
        return $cards;
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
