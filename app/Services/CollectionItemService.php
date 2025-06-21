<?php

namespace App\Services;

use App\Models\UserCollection;
use App\Models\UserCollectionItem;
use App\Models\Card;
use App\Models\CardsVariant;
use App\Models\CardsVariantType;
use App\Models\CardsVariantsPricesMv;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class CollectionItemService
{
    /**
     * Načte všechny položky v dané kolekci s optimalizovaným výběrem sloupců.
     * Používá JOIN dotazy pro skutečnou kontrolu nad načítanými daty.
     */
    public function getItemsForCollection(UserCollection $collection)
    {
        return DB::table('user_collection_items as items')
            ->join('cards as card', 'items.card_id', '=', 'card.id')
            ->join('sets as set', 'card.set_id', '=', 'set.id')
            ->leftJoin('cards_variant as variant', 'items.variant_id', '=', 'variant.cm_id')
            ->leftJoin('cards_variant_types as variant_type', function($join) {
                $join->on('items.variant_type', '=', 'variant_type.code')
                     ->on('variant.variant', '=', 'variant_type.variant');
            })
            ->where('items.collection_id', $collection->id)
            ->select([
                // Z user_collection_items
                'items.id',
                'items.collection_id',
                'items.card_id',
                'items.variant_id',
                'items.variant_type',
                'items.quantity',
                'items.condition',
                'items.language',
                'items.purchase_price',
                'items.created_at',
                
                // Z cards (jen potřebné sloupce)
                'card.name as card_name',
                'card.number as card_number',
                'card.rarity as card_rarity',
                'card.img_small as card_img_small',
                
                // Z sets (jen název)
                'set.name as set_name',
                
                // Z cards_variant (jen potřebné sloupce)
                'variant.variant as variant_type_code',
                
                // Z cards_variant_types
                'variant_type.name as variant_type_name'
            ])
            ->get()
            ->map(function ($item) {
                // Transformujeme flat strukturu do objektové struktury pro kompatibilitu s frontend
                return (object) [
                    'id' => $item->id,
                    'collection_id' => $item->collection_id,
                    'card_id' => $item->card_id,
                    'variant_id' => $item->variant_id,
                    'variant_type' => $item->variant_type,
                    'variant_type_name' => $item->variant_type_name,
                    'quantity' => $item->quantity,
                    'condition' => $item->condition,
                    'language' => $item->language,
                    'purchase_price' => $item->purchase_price,
                    'created_at' => $item->created_at,
                    'card' => (object) [
                        'id' => $item->card_id,
                        'name' => $item->card_name,
                        'number' => $item->card_number,
                        'rarity' => $item->card_rarity,
                        'img_small' => $item->card_img_small,
                        'set_name' => $item->set_name,
                    ],
                    'variant' => $item->variant_id ? (object) [
                        'cm_id' => $item->variant_id,
                        'card_id' => $item->card_id,
                        'variant' => $item->variant_type_code,
                    ] : null
                ];
            });
    }

    /**
     * Přidá novou položku do kolekce.
     * Pokud už existuje položka se stejnými parametry (podle unique constraintu), 
     * zvýší se její množství místo vytvoření duplicitní položky.
     */
    public function addItemToCollection(UserCollection $collection, array $data): UserCollectionItem
    {
        return DB::transaction(function () use ($collection, $data) {
            // Zkontroluj, zda už existuje položka se stejnými parametry
            $existingItem = $collection->items()
                ->where('card_id', $data['card_id'])
                ->where('variant_id', $data['variant_id'])
                ->where('variant_type', $data['variant_type'])
                ->where('condition', $data['condition'])
                ->where('language', $data['language'])
                ->where('purchase_price', $data['purchase_price'])
                ->first();

            if ($existingItem) {
                // Zvýš množství existující položky
                $existingItem->increment('quantity', $data['quantity']);
                return $existingItem->fresh();
            } else {
                // Vytvoř novou položku
                return $collection->items()->create($data);
            }
        });
    }

    /**
     * Aktualizuje existující položku v kolekci.
     */
    public function updateItemInCollection(UserCollection $collection, UserCollectionItem $item, array $data): UserCollectionItem
    {
        // TODO: Validace a business logika
        return DB::transaction(function () use ($item, $data) {
            $item->update($data);
            return $item->fresh();
        });
    }

    /**
     * Smaže položku ze sbírky.
     */
    public function deleteItemFromCollection(UserCollection $collection, UserCollectionItem $item): bool
    {
        // TODO: Oprávnění a případné další business pravidla
        return DB::transaction(function () use ($item) {
            return $item->delete();
        });
    }

    /**
     * Vrátí seznam typů karet (z cards_variant_types) pro danou kartu.
     */
    public function getTypesForCard(string $cardId)
    {
        // 1. Najít všechny varianty pro dané card_id
        $variantIds = CardsVariant::where('card_id', $cardId)
            ->pluck('variant')
            ->unique()
            ->filter(); // Odstraní null hodnoty

        if ($variantIds->isEmpty()) {
            return collect();
        }

        // 2. Najít všechny typy, které odpovídají některé z variant
        // Používáme DISTINCT na code, abychom dostali jen jeden záznam pro každý code
        $types = DB::table('cards_variant_types')
            ->whereIn('variant', $variantIds)
            ->select('code', 'name', 'description')
            ->selectRaw('MIN(variant) as variant') // Vezmeme první variant pro daný code
            ->groupBy('code', 'name', 'description')
            ->get();

        return $types;
    }

    /**
     * Najde konkrétní variantu (cm_id) pro daný typ a kartu.
     */
    public function resolveVariantForType(string $cardId, string $variantTypeCode)
    {
        // 1. Najít všechny varianty pro danou kartu
        $cardVariants = CardsVariant::where('card_id', $cardId)
            ->pluck('variant')
            ->unique()
            ->filter();
            
        if ($cardVariants->isEmpty()) {
            return null;
        }
        
        // 2. Najít záznam v cards_variant_types podle code a variant, který existuje pro tuto kartu
        $type = DB::table('cards_variant_types')
            ->where('code', $variantTypeCode)
            ->whereIn('variant', $cardVariants)
            ->first();
            
        if (!$type) {
            return null;
        }
        
        // 3. Najít variantu v cards_variant podle card_id a variant
        $variant = CardsVariant::where('card_id', $cardId)
            ->where('variant', $type->variant)
            ->first();
            
        return $variant;
    }

    /**
     * Načte všechny dostupné typy cen z `cards_variants_prices_mv` pro danou `variantId` (`cm_id`).
     * Rozlišuje standardní Cardmarket ceny od "reverse holo" Cardmarket cen na základě poskytnutého `$variantTypeCode`.
     */
    public function getVariantPrices(int $variantId, ?int $variantTypeCode = null): ?array
    {
        $priceData = CardsVariantsPricesMv::where('cm_id', $variantId)->first();

        if (!$priceData) {
            return null;
        }

        $cm_prices = [];
        $is_reverse_holo_type = false;

        if ($variantTypeCode) {
            $typeInfo = CardsVariantType::where('code', $variantTypeCode)->first();
            // Předpokládáme, že price_column_suffix pro reverse holo bude obsahovat 'reverse_holo'
            // nebo bude specificky označen. Toto je třeba doladit podle skutečných dat.
            if ($typeInfo && $typeInfo->price_column_suffix && str_contains(strtolower((string)$typeInfo->price_column_suffix), 'reverse_holo')) {
                $is_reverse_holo_type = true;
            }
        }

        if ($is_reverse_holo_type) {
            $cm_prices = [
                'low'   => $priceData->reverse_holo_low ?? null,
                'trend' => $priceData->reverse_holo_trend ?? null,
                'avg1'  => $priceData->reverse_holo_avg1 ?? null,
                'avg7'  => $priceData->reverse_holo_avg7 ?? null,
                'avg30' => $priceData->reverse_holo_avg30 ?? null,
            ];
        } else {
            $cm_prices = [
                'low'   => $priceData->low_price ?? null,
                'trend' => $priceData->trend_price ?? null,
                'avg1'  => $priceData->avg1 ?? null,
                'avg7'  => $priceData->avg7 ?? null,
                'avg30' => $priceData->avg30 ?? null,
            ];
        }

        return [
            'cardmarket' => $cm_prices,
            'tcgplayer' => [
                'low'   => $priceData->tcg_price_low ?? null,
                'trend' => $priceData->tcg_price_trend ?? null, // Ujistěte se, že tento sloupec existuje
                // Zde přidejte další TCGPlayer ceny, pokud jsou v CardsVariantsPricesMv
                // 'mid' => $priceData->tcg_price_mid ?? null,
                // 'high' => $priceData->tcg_price_high ?? null,
                // 'market' => $priceData->tcg_price_market ?? null,
            ]
        ];
    }

    /**
     * Sestaví komplexní datový objekt pro frontend, obsahující informace o kartě, její variantě a všech dostupných cenách.
     */
    public function getVariantDetails(int $variantId): ?array
    {
        $variant = CardsVariant::with('card')->find($variantId);

        if (!$variant || !$variant->card) {
            return null;
        }

        $variantTypeCode = null;
        $variantTypeName = 'Unknown'; // Default
        
        // Získání informací o typu varianty z DB
        // Použijeme $variant->variant, což je číselný klíč pro spojení s cards_variant_types.variant
        if ($variant->variant !== null) {
            $typeInfo = DB::table('cards_variant_types')
                        ->where('variant', $variant->variant)
                        // Pokud jedna hodnota $variant->variant může mít více typů (což by nemělo být u POK), 
                        // museli bychom zde přidat další kritérium, např. podle boolean flagů na CardsVariant modelu
                        // Prozatím předpokládáme, že $variant->variant vede k jednomu typu, nebo bereme první nalezený.
                        ->first(); 
            if ($typeInfo) {
                $variantTypeCode = $typeInfo->code;
                $variantTypeName = $typeInfo->name;
            }
        }
        
        $prices = $this->getVariantPrices($variantId, $variantTypeCode);

        return [
            'card_id'           => $variant->card->id,
            'card_name'         => $variant->card->name,
            'set_id'            => $variant->card->set_id,
            'number'            => $variant->card->number,
            'rarity'            => $variant->card->rarity,
            'supertype'         => $variant->card->supertype,
            'types'             => $variant->card->types, // Předpokládáme, že je to pole
            'image_url'         => $variant->card->img_large, // Nebo img_small dle potřeby
            'variant_id'        => $variant->cm_id,
            'variant_type_code' => $variantTypeCode,
            'variant_type_name' => $variantTypeName,
            'collector_number'  => $variant->collector_number,
            'ptcgo_code'        => $variant->ptcgo_code,
            'tcgplayer_id'      => $variant->tcgplayer_id,
            'prices'            => $prices,
            // Zde můžete přidat další relevantní atributy z $variant->card nebo $variant
            // Např. $variant->date_added, $variant->card->flavor_text atd.
        ];
    }

    /**
     * Připraví základní pole dat pro vytvoření nového `UserCollectionItem`.
     */
    public function mapCardVariantToCollectionItem(string $cardId, int $variantId, int $variantTypeCode): array
    {
        return [
            'card_id'       => $cardId,
            'variant_id'    => $variantId,       // cm_id z CardsVariant
            'variant_type'  => $variantTypeCode, // code z CardsVariantType
            'quantity'      => 1,               // Výchozí hodnota
        ];
    }

    /**
     * Načte položky v kolekci s podporou server-side pagination, filtrování a řazení.
     *
     * @param UserCollection $collection
     * @param array $filters
     * @param string $sortBy
     * @param string $sortDirection
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getItemsForCollectionPaginated(
        UserCollection $collection,
        array $filters = [],
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        int $perPage = 30
    ) {
        $query = DB::table('user_collection_items as items')
            ->join('cards as card', 'items.card_id', '=', 'card.id')
            ->join('sets as set', 'card.set_id', '=', 'set.id')
            ->leftJoin('cards_variant as variant', 'items.variant_id', '=', 'variant.cm_id')
            ->leftJoin('cards_variants_prices_mv as prices', 'items.variant_id', '=', 'prices.cm_id')
            ->leftJoin('cards_variant_types as variant_type', function($join) {
                $join->on('items.variant_type', '=', 'variant_type.code')
                     ->on('variant.variant', '=', 'variant_type.variant');
            })
            ->where('items.collection_id', $collection->id);

        // Filtrování
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('card.name', 'LIKE', "%{$search}%")
                  ->orWhere('card.number', 'LIKE', "%{$search}%");
            });
        }
        if (!empty($filters['condition'])) {
            $query->where('items.condition', $filters['condition']);
        }
        if (!empty($filters['language'])) {
            $query->where('items.language', $filters['language']);
        }
        if (!empty($filters['rarity'])) {
            $query->where('card.rarity', $filters['rarity']);
        }
        if (isset($filters['price_min'])) {
            $query->where('items.purchase_price', '>=', $filters['price_min']);
        }
        if (isset($filters['price_max'])) {
            $query->where('items.purchase_price', '<=', $filters['price_max']);
        }
        if (!empty($filters['date_from'])) {
            $query->where('items.created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('items.created_at', '<=', $filters['date_to']);
        }

        // Řazení
        $validSortColumns = [
            'name' => 'card.name',
            'number' => 'card.number',
            'rarity' => 'card.rarity',
            'condition' => 'items.condition',
            'language' => 'items.language',
            'quantity' => 'items.quantity',
            'price' => 'items.purchase_price',
            'created_at' => 'items.created_at',
        ];
        $sortColumn = $validSortColumns[$sortBy] ?? 'items.created_at';
        $query->orderBy($sortColumn, $sortDirection);

        // Výběr sloupců
        $query->select([
            'items.id', 'items.collection_id', 'items.card_id',
            'items.variant_id', 'items.variant_type', 'items.quantity',
            'items.condition', 'items.language', 'items.purchase_price',
            'items.created_at', 'items.updated_at',
            'card.name as card_name', 'card.number as card_number',
            'card.rarity as card_rarity', 'card.img_small as card_img_small',
            'set.name as set_name',
            'variant.variant as variant_type_code',
            'variant_type.name as variant_type_name',
            // Tržní ceny z CardMarket - výběr podle variant_type
            DB::raw("CASE 
                WHEN (SELECT price_column_suffix FROM cards_variant_types WHERE code = items.variant_type AND variant = variant.variant LIMIT 1) = 'reverse_holo' THEN prices.reverse_holo_avg30 
                ELSE prices.avg30 
            END as market_price_avg30"),
            DB::raw("CASE 
                WHEN (SELECT price_column_suffix FROM cards_variant_types WHERE code = items.variant_type AND variant = variant.variant LIMIT 1) = 'reverse_holo' THEN prices.reverse_holo_trend 
                ELSE prices.trend_price 
            END as market_price_trend"),
            DB::raw("CASE 
                WHEN (SELECT price_column_suffix FROM cards_variant_types WHERE code = items.variant_type AND variant = variant.variant LIMIT 1) = 'reverse_holo' THEN prices.reverse_holo_low 
                ELSE prices.low_price 
            END as market_price_low"),
            'prices.cm_updated_at as market_price_updated'
        ]);

        // Pagination
        $paginated = $query->paginate($perPage);

        // Transformace položek (stejná jako v getItemsForCollection)
        $paginated->getCollection()->transform(function ($item) {
            return (object) [
                'id' => $item->id,
                'collection_id' => $item->collection_id,
                'card_id' => $item->card_id,
                'variant_id' => $item->variant_id,
                'variant_type' => $item->variant_type,
                'variant_type_name' => $item->variant_type_name,
                'quantity' => $item->quantity,
                'condition' => $item->condition,
                'language' => $item->language,
                'purchase_price' => $item->purchase_price,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'card' => (object) [
                    'id' => $item->card_id,
                    'name' => $item->card_name,
                    'number' => $item->card_number,
                    'rarity' => $item->card_rarity,
                    'img_small' => $item->card_img_small,
                    'set_name' => $item->set_name,
                ],
                'variant' => $item->variant_id ? (object) [
                    'cm_id' => $item->variant_id,
                    'card_id' => $item->card_id,
                    'variant' => $item->variant_type_code,
                ] : null,
                // Tržní ceny z CardMarket
                'market_prices' => (object) [
                    'avg30' => $item->market_price_avg30,
                    'trend' => $item->market_price_trend,
                    'low' => $item->market_price_low,
                    'updated_at' => $item->market_price_updated,
                ]
            ];
        });

        return $paginated;
    }

    /**
     * Vrátí základní statistiky kolekce: počet karet, unikátních karet, celkovou hodnotu.
     *
     * @param UserCollection $collection
     * @return array
     */
    public function getCollectionStats(UserCollection $collection): array
    {
        $query = DB::table('user_collection_items')
            ->where('collection_id', $collection->id);

        $totalCards = $query->sum('quantity');
        $uniqueCards = $query->distinct('card_id')->count('card_id');
        
        // Výpočet celkové pořizovací hodnoty
        $totalPurchaseValue = DB::table('user_collection_items')
            ->where('collection_id', $collection->id)
            ->selectRaw('COALESCE(SUM(COALESCE(purchase_price, 0) * quantity), 0) as total')
            ->value('total');

        // Výpočet celkové tržní hodnoty (avg30)
        $totalMarketValue = DB::table('user_collection_items as items')
            ->leftJoin('cards_variants_prices_mv as prices', 'items.variant_id', '=', 'prices.cm_id')
            ->where('items.collection_id', $collection->id)
            ->selectRaw('COALESCE(SUM(COALESCE(prices.avg30, 0) * items.quantity), 0) as total')
            ->value('total');

        return [
            'total_cards' => (int) $totalCards,
            'unique_cards' => (int) $uniqueCards,
            'total_purchase_value' => (float) $totalPurchaseValue,
            'total_market_value' => (float) $totalMarketValue,
            'value_difference' => (float) ($totalMarketValue - $totalPurchaseValue),
        ];
    }

    /**
     * Vrátí možnosti pro dropdowny filtrů (rarity, jazyky, condition) v rámci kolekce.
     *
     * @param UserCollection $collection
     * @return array
     */
    public function getFilterOptions(UserCollection $collection): array
    {
        $baseQuery = DB::table('user_collection_items as items')
            ->join('cards as card', 'items.card_id', '=', 'card.id')
            ->where('items.collection_id', $collection->id);

        $rarities = $baseQuery->distinct()->pluck('card.rarity')->filter()->unique()->values()->all();
        $languages = $baseQuery->distinct()->pluck('items.language')->filter()->unique()->values()->all();
        $conditions = $baseQuery->distinct()->pluck('items.condition')->filter()->unique()->values()->all();

        return [
            'rarities' => $rarities,
            'languages' => $languages,
            'conditions' => $conditions,
        ];
    }
} 