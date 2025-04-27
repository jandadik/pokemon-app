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
            // Načtení karet s cenami pomocí JOIN na materializovaný pohled
            $cards = $set->cards()
                ->select([
                    'cards.id', 
                    'cards.set_id', 
                    'cards.name', 
                    'cards.number', 
                    'cards.supertype', 
                    'cards.types', 
                    'cards.rarity', 
                    'cards.hp', 
                    'cards.img_small',
                    'cards.img_file_small',
                    // Ceny z materializovaného pohledu
                    'cp.cm_price_low as price_cm_low',
                    'cp.cm_price_trend as price_cm_trend',
                    'cp.cm_avg7 as price_cm_avg7',
                    'cp.cm_avg30 as price_cm_avg30',
                    'cp.cm_updated_at as price_cm_updated_at'
                ])
                ->leftJoin('cards_prices_mv as cp', 'cards.id', '=', 'cp.card_id')
                ->orderBy('cards.number')
                ->get();
                
            // Mapování hodnot pro zpětnou kompatibilitu
            foreach ($cards as $card) {
                if ($card->price_cm_avg30) {
                    $card->prices_cm = (object)[
                        'avg30' => $card->price_cm_avg30,
                        'avg7' => $card->price_cm_avg7,
                        'avg1' => $card->price_cm_trend, // Používáme trend místo avg1
                        'reverse_holo_avg30' => null, // Toto pole není v materializovaném pohledu
                        'trend_price' => $card->price_cm_trend,
                        'updated_at' => $card->price_cm_updated_at,
                        'card_id' => $card->id
                    ];
                }
                
                // Odstranění sloupců, které jsou nyní v prices_cm objektu
                unset(
                    $card->price_cm_low,
                    $card->price_cm_trend,
                    $card->price_cm_avg7,
                    $card->price_cm_avg30,
                    $card->price_cm_updated_at
                );
            }
            
            // Log počet karet s cenami
            $cardsWithPrices = $cards->filter(function($card) {
                return isset($card->prices_cm);
            })->count();
            
            \Log::info("Set {$set->id}: {$cardsWithPrices} karet s cenami z celkem " . count($cards) . " karet.");
            
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
                    'cards.id', 
                    'cards.set_id', 
                    'cards.name', 
                    'cards.number', 
                    'cards.supertype', 
                    'cards.types', 
                    'cards.rarity', 
                    'cards.hp', 
                    'cards.img_small',
                    'cards.img_file_small',
                    // Ceny z materializovaného pohledu
                    'cp.cm_price_low as price_cm_low',
                    'cp.cm_price_trend as price_cm_trend',
                    'cp.cm_avg7 as price_cm_avg7',
                    'cp.cm_avg30 as price_cm_avg30',
                    'cp.cm_updated_at as price_cm_updated_at'
                ])
                ->leftJoin('cards_prices_mv as cp', 'cards.id', '=', 'cp.card_id')
                ->orderBy('cards.number');

            // Aplikace filtrů
            if ($search) {
                $query->where('cards.name', 'like', "%{$search}%");
            }
            if ($type) {
                $query->where('cards.supertype', $type);
            }
            if ($rarity) {
                $query->where('cards.rarity', $rarity);
            }

            // Načtení karet
            $cards = $query->get();
            
            // Mapování hodnot pro zpětnou kompatibilitu
            foreach ($cards as $card) {
                if ($card->price_cm_avg30) {
                    $card->prices_cm = (object)[
                        'avg30' => $card->price_cm_avg30,
                        'avg7' => $card->price_cm_avg7,
                        'avg1' => $card->price_cm_trend, // Používáme trend místo avg1
                        'reverse_holo_avg30' => null, // Toto pole není v materializovaném pohledu
                        'trend_price' => $card->price_cm_trend,
                        'updated_at' => $card->price_cm_updated_at,
                        'card_id' => $card->id
                    ];
                }
                
                // Odstranění sloupců, které jsou nyní v prices_cm objektu
                unset(
                    $card->price_cm_low,
                    $card->price_cm_trend,
                    $card->price_cm_avg7,
                    $card->price_cm_avg30,
                    $card->price_cm_updated_at
                );
            }
            
            // Log počet karet s cenami
            $cardsWithPrices = $cards->filter(function($card) {
                return isset($card->prices_cm);
            })->count();
            
            \Log::info("Set {$set->id}: {$cardsWithPrices} karet s cenami z celkem " . count($cards) . " karet.");
            
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
