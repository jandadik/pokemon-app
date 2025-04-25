<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Card;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class CardController extends Controller
{
    

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        \Log::debug('CardController@index received request with data:', $request->all());

        $perPage = $request->input('per_page', 30);
        $page = $request->input('page', 1);
        $search = $request->input('search', '');
        $setId = $request->input('set_id', '');
        $type = $request->input('type', '');
        $rarity = $request->input('rarity', '');
        $sortBy = $request->input('sort_by', 'name');
        $sortDirection = $request->input('sort_direction', 'asc');

        // Validace vstupních parametrů
        if (!is_numeric($page) || $page < 1) {
            $page = 1;
        }
        if (!in_array((int)$perPage, [30, 60, 120])) {
            $perPage = 30;
        }
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        // Validace sortBy - AKTUALIZOVÁNO pro cards_prices_mv
        $allowedSortColumns = [
            'name', 'number', 'set.name', 'rarity', 
            'cm_avg30' // Sloupce z cards_prices_mv
        ]; 
        if (!in_array($sortBy, $allowedSortColumns)) {
            // Zde můžete logovat chybu nebo použít default
             \Log::warning("Neplatný sloupec pro řazení: {$sortBy}. Používám 'name'.");
            $sortBy = 'name';
        }
        
        \Log::debug('Parametry pro getCards:', [
            'perPage' => $perPage,
            'search' => $search,
            'setId' => $setId,
            'type' => $type,
            'rarity' => $rarity,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection
        ]);
        // Přímé volání getCards bez cache celého výsledku
        $cards = $this->getCards($perPage, $search, $setId, $type, $rarity, $sortBy, $sortDirection);


        return inertia('Card/Index', [
            'cards' => $cards,
            'filters' => $request->only(['search', 'set_id', 'type', 'rarity', 'sort_by', 'sort_direction', 'per_page']), // Použít only pro čistotu
        ]);
    }

    /**
     * Získá karty s možností řazení podle ceny a standardních sloupců
     */
    private function getCards($perPage, $search, $setId, $type, $rarity, $sortBy, $sortDirection): LengthAwarePaginator
    {
        $query = Card::query() // Použít ::query() pro lepší přehlednost
            ->select([
                'cards.id', 'cards.set_id', 'cards.name', 'cards.number', 
                'cards.supertype', 'cards.types', 'cards.rarity', 'cards.hp', 
                'cards.img_file_small', 'cards.img_small',
                // Explicitně vybrat sloupce z cards
                // Přidání sloupců z cards_prices_mv s aliasy pro jasnost a prevenci kolizí
                'cp.tcg_price_market as price_tcg_market',
                'cp.tcg_price_low as price_tcg_low',
                'cp.tcg_price_mid as price_tcg_mid',
                'cp.tcg_price_high as price_tcg_high',
                'cp.tcg_price_direct_low as price_tcg_direct_low',
                'cp.tcg_updated_at as price_tcg_updated_at',
                'cp.cm_price_low as price_cm_low',
                'cp.cm_price_trend as price_cm_trend',
                'cp.cm_price_avg as price_cm_avg', // Může být užitečné
                'cp.cm_price_suggested as price_cm_suggested',
                'cp.cm_avg7 as price_cm_avg7',
                'cp.cm_avg30 as price_cm_avg30',
                'cp.cm_updated_at as price_cm_updated_at',
            ])
            ->leftJoin('cards_prices_mv as cp', 'cards.id', '=', 'cp.card_id') // LEFT JOIN na materializované view
            ->with(['set:id,name,series']); // Eager load set

        // Fulltextové vyhledávání
        if ($search) {
            $query->whereRaw('MATCH(cards.name, cards.number_txt) AGAINST(? IN BOOLEAN MODE)', [$search . '*']);
        }

        // Ostatní filtry
        if ($setId) {
            $query->where('cards.set_id', $setId);
        }
        if ($type) {
            $query->where('cards.supertype', $type);
        }
        if ($rarity) {
            $query->where('cards.rarity', $rarity);
        }

        // Řazení - ZJEDNODUŠENO s cards_prices_mv
        $priceSortColumns = ['cm_avg30']; // Sloupce pro řazení podle ceny

        if (in_array($sortBy, $priceSortColumns)) {
            // Mapování sortBy na skutečný název sloupce v joinu (s aliasem, pokud jsme ho definovali)
            $priceColumnToSortBy = 'cp.' . $sortBy; // Používáme prefix aliasu tabulky
            
            // NULLS LAST řazení
            $query->orderByRaw("{$priceColumnToSortBy} IS NULL ASC"); // Seřadí non-NULL (0) před NULL (1)
            $query->orderBy($priceColumnToSortBy, $sortDirection);

             // Přidání sekundárního řazení pro konzistenci
             $query->orderBy('cards.name', 'asc'); 

        } else if ($sortBy === 'set.name') {
             // Řazení podle názvu setu stále vyžaduje JOIN na sets
             // Poznámka: Pokud 'set.name' nefunguje kvůli aliasům v selectu, možná bude potřeba upravit join/select zde
             $query->join('sets', 'cards.set_id', '=', 'sets.id')
                   // Odstranit 'cards.*' a 'sets.name as set_name' z předchozí verze, protože select je definován výše
                   ->orderBy('sets.name', $sortDirection)
                   ->orderBy('cards.name', 'asc'); // Sekundární řazení
        }
        else {
            // Standardní řazení podle sloupců v tabulce 'cards'
             if (Schema::hasColumn('cards', $sortBy)) { // Ověření existence sloupce
                $query->orderBy("cards.{$sortBy}", $sortDirection);
             } else {
                 // Fallback
                 \Log::warning("Pokus o řazení podle neexistujícího sloupce v 'cards': {$sortBy}. Používám 'cards.name'.");
                 $query->orderBy('cards.name', 'asc');
             }
        }

        // Stránkování
        $cards = $query->paginate($perPage)->withQueryString();

        // $this->loadCardPrices($cards); // ODSTRANĚNO - ceny jsou již načteny přes JOIN

        return $cards;
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

}