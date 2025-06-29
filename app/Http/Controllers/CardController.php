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
use App\Models\Set;
use App\Services\CardService;
use Illuminate\Http\JsonResponse;
use App\Services\CollectionItemService;
use App\Services\CardImageService;
use App\Services\CollectionService;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    protected CardService $cardService;
    protected CollectionItemService $itemService;
    protected CardImageService $imageService;
    protected CollectionService $collectionService;

    public function __construct(CardService $cardService, CollectionItemService $itemService, CardImageService $imageService, CollectionService $collectionService)
    {
        $this->cardService = $cardService;
        $this->itemService = $itemService;
        $this->imageService = $imageService;
        $this->collectionService = $collectionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //\Log::debug('CardController@index received request with data:', $request->all());

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
        
        // \Log::debug('Parametry pro getCards:', [
        //     'perPage' => $perPage,
        //     'search' => $search,
        //     'setId' => $setId,
        //     'type' => $type,
        //     'rarity' => $rarity,
        //     'sortBy' => $sortBy,
        //     'sortDirection' => $sortDirection
        // ]);
        // Přímé volání getCards bez cache celého výsledku
        $cards = $this->getCards($perPage, $search, $setId, $type, $rarity, $sortBy, $sortDirection);

        // Načtení všech setů pro filtr (s cachováním)
        $sets = Cache::remember('all_sets_for_filter', 3600, function () {
            return Set::select(['id', 'name'])->orderBy('name')->get();
        });

        return inertia('Card/Index', [
            'cards' => $cards,
            'filters' => $request->only(['search', 'set_id', 'type', 'rarity', 'sort_by', 'sort_direction', 'per_page']), // Použít only pro čistotu
            'sets' => $sets, // Přidat sety do props
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
            // Rozdělení hledaného výrazu na slova
            $searchTerms = preg_split('/\s+/', trim($search), -1, PREG_SPLIT_NO_EMPTY);
            // Přidání '+' a '*' ke každému slovu pro BOOLEAN MODE
            $booleanSearchQuery = implode(' ', array_map(function($term) {
                return '+' . $term . '*';
            }, $searchTerms));

            if (!empty($booleanSearchQuery)) {
                $query->whereRaw('MATCH(cards.name, cards.number_txt, cards.ptcgo_code) AGAINST(? IN BOOLEAN MODE)', [$booleanSearchQuery]);
            }
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
    public function show(Card $card, Request $request)
    {
        // Explicitní načtení pouze konkrétního setu pro danou kartu
        $card->load(['set' => function($query) use ($card) {
            $query->select('id', 'name', 'series', 'release_date')
                  ->where('id', $card->set_id);
        }]);
        
        // Explicitní načtení útoků pro konkrétní kartu
        $card->load(['attacks' => function($query) use ($card) {
            $query->select('id', 'card_id', 'name', 'cost', 'damage', 'text')
                  ->where('card_id', $card->id);
        }]);

        // Načtení cenových dat z materializovaného view
        $priceData = DB::table('cards_prices_mv')
            ->where('card_id', $card->id)
            ->first();
            
        if ($priceData) {
            // Připojení cenových dat ke kartě - TCG ceny
            $card->price_tcg_market = $priceData->tcg_price_market;
            $card->price_tcg_low = $priceData->tcg_price_low;
            $card->price_tcg_mid = $priceData->tcg_price_mid;
            $card->price_tcg_high = $priceData->tcg_price_high;
            $card->price_tcg_direct_low = $priceData->tcg_price_direct_low;
            $card->price_tcg_updated_at = $priceData->tcg_updated_at;
            
            // TCG reverse holo ceny
            $card->price_tcg_reverse_holo_market = $priceData->tcg_reverse_holo_price_market ?? null;
            $card->price_tcg_reverse_holo_low = $priceData->tcg_reverse_holo_price_low ?? null;
            $card->price_tcg_reverse_holo_mid = $priceData->tcg_reverse_holo_price_mid ?? null;
            $card->price_tcg_reverse_holo_high = $priceData->tcg_reverse_holo_price_high ?? null;
            
            // CM ceny - normal
            $card->price_cm_low = $priceData->cm_price_low;
            $card->price_cm_avg7 = $priceData->cm_avg7;
            $card->price_cm_avg30 = $priceData->cm_avg30;
            $card->price_cm_trend = $priceData->cm_price_trend;
            $card->price_cm_updated_at = $priceData->cm_updated_at;
            
            // CM ceny - další, které mohou být potřebné
            $card->price_cm_avg = $priceData->cm_price_avg; 
            $card->price_cm_suggested = $priceData->cm_price_suggested;
            
            // CM reverse holo ceny
            $card->price_cm_reverse_holo_low = $priceData->cm_reverse_holo_low ?? null;
            $card->price_cm_reverse_holo_avg7 = $priceData->cm_reverse_holo_avg7 ?? null;
            $card->price_cm_reverse_holo_avg30 = $priceData->cm_reverse_holo_avg30 ?? null;
            $card->price_cm_reverse_holo_trend = $priceData->cm_reverse_holo_trend ?? null;
            $card->price_cm_reverse_holo_sell = $priceData->cm_reverse_holo_sell ?? null;
            $card->price_cm_reverse_holo_avg1 = $priceData->cm_reverse_holo_avg1 ?? null;
        }

        // Získání referreru z URL - odkud byla stránka volána
        $referrer = $request->query('referrer', null);

        // Načtení kolekcí pro přihlášeného uživatele (pokud je přihlášen)
        $userCollections = [];
        if (Auth::check()) {
            $userCollections = $this->collectionService->getUserCollections(Auth::user());
        }

        return inertia('Card/Show', [
            'card' => $card,
            'referrer' => $referrer,
            'userCollections' => $userCollections
        ]);
    }

    // Nová metoda pro lookup
    public function performLookup(Request $request): JsonResponse
    {
        $queryString = $request->input('query', '');
        $limit = (int) $request->input('limit', 15);

        if (empty($queryString) || mb_strlen($queryString) < 2) {
            return response()->json([]);
        }

        $results = $this->cardService->lookupCards($queryString, $limit);

        return response()->json($results);
    }

    /**
     * Vrátí seznam typů karet pro konkrétní kartu (AJAX endpoint).
     * OPTIMALIZOVÁNO: Při jediné variantě vrací rovnou kompletní detaily.
     */
    public function variants(string $cardId)
    {
        $types = $this->itemService->getTypesForCard($cardId);
        
        // Pokud je dostupná pouze jedna varianta, vrátíme rovnou kompletní detaily
        if ($types->count() === 1) {
            $singleType = $types->first();
            $details = \App\Models\CardsVariantsTypesMv::getCompleteVariantDetails($cardId, $singleType->code);
            
            if ($details) {
                // Vrátíme formát kompatibilní s frontend - seznam s jednou položkou obsahující detaily
                return response()->json([
                    [
                        'code' => $singleType->code,  
                        'name' => $singleType->name,
                        'description' => $singleType->description,
                        'variant' => $singleType->variant,
                        // Přidáme indikátor, že obsahuje kompletní detaily
                        'has_details' => true,
                        // Přidáme kompletní detaily
                        'details' => $details
                    ]
                ]);
            }
        }
        
        // Standardní odpověď pro více variant (bez detailů)
        return response()->json($types->map(function($type) {
            return [
                'code' => $type->code,
                'name' => $type->name, 
                'description' => $type->description,
                'variant' => $type->variant,
                'has_details' => false
            ];
        }));
    }

    /**
     * Vrátí detailní informace o variantě pro konkrétní kartu a typ (AJAX endpoint).
     */
    public function variantDetails(string $cardId, string $variantTypeCode)
    {
        // OPTIMALIZOVÁNO: Použít materialized view místo složitých JOIN dotazů
        $details = \App\Models\CardsVariantsTypesMv::getCompleteVariantDetails($cardId, $variantTypeCode);
        
        if (!$details) {
            return response()->json(['error' => 'Variant not found'], 404);
        }
        
        return response()->json($details);
    }
    
    /**
     * Get bulk image data for multiple cards (for PokeImage component)
     */
    public function getBulkImageData(Request $request)
    {
        $request->validate([
            'card_ids' => 'required|array|max:50', // Limit to 50 cards per request
            'card_ids.*' => 'required|string',
            'size' => 'sometimes|string|in:small,large'
        ]);
        
        $cardIds = $request->input('card_ids');
        $size = $request->input('size', 'small');
        
        $imageData = $this->imageService->getBulkImageData($cardIds, $size);
        
        return response()->json([
            'imageData' => $imageData,
            'success' => true
        ]);
    }
}