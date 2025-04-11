<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Set;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class SetController extends Controller
{
    private $seriesOrder = [
        'Scarlet & Violet',
        'Sword & Shield',
        'Sun & Moon',
        'XY',
        'Black & White',
        'HeartGold & SoulSilver',
        'Platinum',
        'Diamond & Pearl',
        'EX',
        'e-Card',
        'Neo',
        'Gym',
        'Base',
        'Other'
    ];

    /**
     * Zobrazení seznamu setů
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $series = $request->get('series', '');
        $sortBy = $request->get('sort_by', 'release_date');
        $sortDirection = $request->get('sort_direction', 'desc');

        // Validace směru řazení
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        // Validace sloupce pro řazení
        $allowedSortColumns = ['release_date', 'name', 'series', 'total'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'release_date';
        }

        // Cache key pro seznam setů s filtry
        $cacheKey = "sets_list_search_{$search}_series_{$series}_sort_{$sortBy}_{$sortDirection}";

        $sets = Cache::remember($cacheKey, 3600, function () use ($search, $series, $sortBy, $sortDirection) {
            $query = Set::select([
                'id',
                'name',
                'series',
                'release_date',
                'total',
                'logo_url',
                'symbol_url',
                'ptcgo_code'
            ]);

            // Aplikace filtrů
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('series', 'like', "%{$search}%");
                });
            }
            if ($series) {
                $query->where('series', $series);
            }

            // Aplikace řazení
            if ($sortBy === 'series') {
                $query->orderByRaw("FIELD(series, ?)", [implode(',', $this->seriesOrder)])
                    ->orderBy('release_date', $sortDirection);
            } else {
                $query->orderBy($sortBy, $sortDirection);
            }

            $sets = $query->get();
            
            // Načteme tržní ceny pro každý set
            foreach ($sets as $set) {
                // Cache key pro cenu setu
                $priceCacheKey = "set_price_{$set->id}";
                
                // Načtení ceny s cachováním (na 24 hodin)
                $set->market_price = Cache::remember($priceCacheKey, 86400, function () use ($set) {
                    return $set->getMarketPrice();
                });
            }
            
            return $sets;
        });

        // Získání unikátních sérií pro filtr
        $seriesList = Cache::remember('sets_series_list', 3600, function () {
            return Set::distinct()->pluck('series')->sort()->values();
        });

        return inertia('Set/Index', [
            'sets' => [
                'data' => $sets
            ],
            'filters' => [
                'search' => $search,
                'series' => $series,
                'sort_by' => $sortBy,
                'sort_direction' => $sortDirection
            ],
            'seriesList' => $seriesList
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
    public function show(Set $set)
    {
        // Cache key pro set
        $setCacheKey = "set_detail_{$set->id}";
        
        // Načtení setu s cachováním
        $set = Cache::remember($setCacheKey, 3600, function () use ($set) {
            // Načteme tržní cenu pro set
            $set->market_price = $set->getMarketPrice();
            return $set;
        });

        // Cache key pro karty
        $cardsCacheKey = "set_{$set->id}_all_cards";

        $cards = Cache::remember($cardsCacheKey, 3600, function () use ($set) {
            // Načtení karet
            $cards = $set->cards()
                ->select([
                    'id', 
                    'set_id', 
                    'name', 
                    'number', 
                    'supertype', 
                    'types', 
                    'rarity', 
                    'hp', 
                    'img_small',
                    'img_file_small'
                ])
                ->orderBy('number')
                ->get();
                
            // Pro každou kartu načteme ceny z CardMarket
            $cardsWithPrices = 0;
            $samplePrice = null;
            $sampleCardId = null;
            
            // Nejprve získáme nejnovější updated_at pro všechny karty v tomto setu
            $latestCardUpdates = DB::table('prices_cm')
                ->where('card_id', 'like', $set->id . '-%')
                ->select('card_id', DB::raw('MAX(updated_at) as latest_update'))
                ->groupBy('card_id')
                ->get()
                ->keyBy('card_id');
            
            // Pro ladící účely
            \Log::info("Set {$set->id}: Nalezeno " . count($latestCardUpdates) . " záznamů cen");
            
            // Načtení nejaktuálnějších cen pro každou kartu
            foreach ($cards as $card) {
                // Vytvoříme card_id ve stejném formátu jako se používá v tabulce prices_cm
                $cardId = $set->id . '-' . $card->number;
                
                // Pokud existuje záznam pro tuto kartu
                if (isset($latestCardUpdates[$cardId])) {
                    // Získáme nejnovější cenu pro tuto kartu
                    $latestUpdate = $latestCardUpdates[$cardId]->latest_update;
                    
                    $priceData = DB::table('prices_cm')
                        ->where('card_id', $cardId)
                        ->where('updated_at', $latestUpdate)
                        ->first();
                    
                    if ($priceData) {
                        $cardsWithPrices++;
                        if (!$samplePrice) {
                            $samplePrice = $priceData->avg30;
                            $sampleCardId = $priceData->card_id;
                        }
                        
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
            
            // Log počet karet s cenami
            \Log::info("Set {$set->id}: {$cardsWithPrices} karet s cenami z celkem " . count($cards) . " karet. Sample price: {$samplePrice} pro kartu {$sampleCardId}");
            
            return $cards;
        });

        // Připojíme karty k setu pro předání do Vue komponenty
        $set->cards = $cards;

        return inertia('Set/Show', [
            'set' => $set,
            'filters' => [
                'search' => '',
                'type' => '',
                'rarity' => ''
            ]
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

    /**
     * Zobrazení karet konkrétního setu
     */
    public function cards(Request $request, Set $set)
    {
        $search = $request->get('search', '');
        $type = $request->get('type', '');
        $rarity = $request->get('rarity', '');

        // Cache key pro set
        $setCacheKey = "set_detail_{$set->id}";
        
        // Načtení setu s cachováním
        $set = Cache::remember($setCacheKey, 3600, function () use ($set) {
            // Načteme tržní cenu pro set
            $set->market_price = $set->getMarketPrice();
            return $set;
        });

        // Cache key pro karty s filtry
        $cardsCacheKey = "set_{$set->id}_cards_search_{$search}_type_{$type}_rarity_{$rarity}";

        $cards = Cache::remember($cardsCacheKey, 3600, function () use ($set, $search, $type, $rarity) {
            $query = $set->cards()
                ->select([
                    'id', 
                    'set_id', 
                    'name', 
                    'number', 
                    'supertype', 
                    'types', 
                    'rarity', 
                    'hp', 
                    'img_small',
                    'img_file_small'
                ])
                ->orderBy('number');

            // Aplikace filtrů
            if ($search) {
                $query->where('name', 'like', "%{$search}%");
            }
            if ($type) {
                $query->where('supertype', $type);
            }
            if ($rarity) {
                $query->where('rarity', $rarity);
            }

            // Načtení karet
            $cards = $query->get();
            
            // Pro každou kartu načteme ceny z CardMarket
            $cardsWithPrices = 0;
            $samplePrice = null;
            $sampleCardId = null;
            
            // Nejprve získáme nejnovější updated_at pro všechny karty v tomto setu
            $latestCardUpdates = DB::table('prices_cm')
                ->where('card_id', 'like', $set->id . '-%')
                ->select('card_id', DB::raw('MAX(updated_at) as latest_update'))
                ->groupBy('card_id')
                ->get()
                ->keyBy('card_id');
            
            // Pro ladící účely
            \Log::info("Set {$set->id}: Nalezeno " . count($latestCardUpdates) . " záznamů cen");
            
            // Načtení nejaktuálnějších cen pro každou kartu
            foreach ($cards as $card) {
                // Vytvoříme card_id ve stejném formátu jako se používá v tabulce prices_cm
                $cardId = $set->id . '-' . $card->number;
                
                // Pokud existuje záznam pro tuto kartu
                if (isset($latestCardUpdates[$cardId])) {
                    // Získáme nejnovější cenu pro tuto kartu
                    $latestUpdate = $latestCardUpdates[$cardId]->latest_update;
                    
                    $priceData = DB::table('prices_cm')
                        ->where('card_id', $cardId)
                        ->where('updated_at', $latestUpdate)
                        ->first();
                    
                    if ($priceData) {
                        $cardsWithPrices++;
                        if (!$samplePrice) {
                            $samplePrice = $priceData->avg30;
                            $sampleCardId = $priceData->card_id;
                        }
                        
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
            
            // Log počet karet s cenami
            \Log::info("Set {$set->id}: {$cardsWithPrices} karet s cenami z celkem " . count($cards) . " karet. Sample price: {$samplePrice} pro kartu {$sampleCardId}");
            
            return $cards;
        });

        // Připojíme karty k setu pro předání do Vue komponenty
        $set->cards = $cards;

        return inertia('Set/Show', [
            'set' => $set,
            'filters' => [
                'search' => $search,
                'type' => $type,
                'rarity' => $rarity
            ]
        ]);
    }
}
