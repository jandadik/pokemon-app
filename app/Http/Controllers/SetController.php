<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Set;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

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

            return $query->get();
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
        Gate::authorize(ability: 'admin.access');
        $set->load(['cards' => function ($query) {
            $query->select(['id', 'set_id', 'name', 'number', 'supertype', 'types', 'rarity'])
                ->orderBy('number');
        }]);

        return inertia('Set/Show', [
            'set' => $set
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

        // Cache key pro filtry
        $cacheKey = "set_{$set->id}_cards_search_{$search}_type_{$type}_rarity_{$rarity}";

        $cards = Cache::remember($cacheKey, 3600, function () use ($set, $search, $type, $rarity) {
            $query = $set->cards()
                ->select(['id', 'set_id', 'name', 'number', 'supertype', 'types', 'rarity', 'hp'])
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

            return $query->get();
        });

        return inertia('Set/Cards', [
            'set' => $set,
            'cards' => $cards,
            'filters' => [
                'search' => $search,
                'type' => $type,
                'rarity' => $rarity
            ]
        ]);
    }
}
