# Laravel Services Documentation

This document provides comprehensive documentation of the service layer in the Pokemon Card Collection application.

## Service Layer Overview

The application implements a **Service Layer Pattern** where business logic is abstracted from controllers into dedicated service classes. Services handle:

- Complex data queries and transformations
- Business rule enforcement
- Transaction management
- Caching strategies
- Authorization checks

## Service Files Summary

| Service | File Size | Primary Responsibility |
|---------|-----------|------------------------|
| CardImageService | 4,677 bytes | Image retrieval and caching |
| CardService | 4,176 bytes | Card search and lookup |
| CollectionService | 9,300 bytes | Collection CRUD and management |
| CollectionItemService | 21,186 bytes | Item management, variants, pricing |
| **Total** | **~39 KB** | Core business logic |

---

## CardImageService

**File:** `app/Services/CardImageService.php`

Manages card image retrieval, caching, and bulk loading operations.

### Purpose
- Provide optimized image URL resolution
- Cache image data perpetually for performance
- Support multiple image sizes (small, large)
- Handle fallback images when primary unavailable

### Dependencies
- `Card` model
- Laravel `Cache` facade

### Public Methods

#### `getImageData($cardId, $size = 'small')`
Retrieves cached image data for a single card.

```php
$imageService->getImageData('xy1-1', 'small');
// Returns:
[
    'url' => '/storage/cards/xy1-1_small.png',
    'fallback' => 'https://images.pokemontcg.io/xy1/1.png',
    'cardId' => 'xy1-1',
    'size' => 'small',
    'cached_at' => '2024-01-15 10:30:00'
]
```

**Caching:** Perpetual (`Cache::rememberForever`)

---

#### `getBulkImageData(array $cardIds, $size = 'small'): array`
Loads image data for multiple cards efficiently.

```php
$imageService->getBulkImageData(['xy1-1', 'xy1-2', 'xy1-3'], 'large');
// Returns associative array keyed by card ID
```

**Features:**
- Single database query for all cards
- MD5-hashed cache key for bulk operations
- Returns placeholder for missing cards

---

#### `getSetImageData($setId, $size = 'small')`
Preloads all card images for a specific set.

```php
$imageService->getSetImageData('xy1', 'small');
```

**Use Case:** Set browsing pages where all cards need images

---

#### `preloadCollectionImages($collectionId, $size = 'small')`
Preloads images for all cards in a user's collection.

```php
$imageService->preloadCollectionImages(123, 'small');
```

**Logic:**
1. Query unique card IDs from collection items
2. Bulk load image data
3. Cache for future requests

---

#### `clearCardCache($cardId)`
Cache maintenance - clears both small and large image caches.

```php
$imageService->clearCardCache('xy1-1');
```

**Use Case:** When card images are updated

### Private Methods

#### `buildImageData($card, $size)`
Constructs image data structure with priority logic:

1. Local file (if exists)
2. External URL from database
3. Constructed path
4. Placeholder image

Returns structured array with URL, fallback, metadata.

#### `getPlaceholderData()`
Returns standardized placeholder for missing images.

### Caching Strategy

```php
// Individual card cache
Cache::rememberForever("card_image_{$cardId}_{$size}", ...)

// Bulk cache with MD5 hash
$cacheKey = 'bulk_images_' . md5(implode(',', $cardIds) . $size);
Cache::rememberForever($cacheKey, ...)

// Set-wide cache
Cache::rememberForever("set_images_{$setId}_{$size}", ...)
```

---

## CardService

**File:** `app/Services/CardService.php`

Provides card search and lookup functionality with full-text search optimization.

### Purpose
- Fast card search via MySQL full-text indexing
- Search query sanitization and optimization
- Rapid card lookup for autocomplete features

### Dependencies
- `Card` model
- Laravel `DB` facade

### Public Methods

#### `prepareBooleanSearchQuery(string $queryString): string`
Sanitizes and prepares search input for MySQL boolean full-text mode.

```php
$cardService->prepareBooleanSearchQuery('Charizard EX');
// Returns: '+Charizard* +EX*'
```

**Logic:**
1. Remove special characters (except spaces)
2. Split into terms
3. Add `+` prefix (AND logic)
4. Add `*` suffix (wildcard matching)
5. Filter out short numeric terms (<3 chars) due to `innodb_ft_min_token_size`

**Security:** Input sanitization prevents SQL injection

---

#### `lookupCards(string $queryString, int $limit = 15): Collection`
Performs rapid card lookup via full-text search.

```php
$cardService->lookupCards('Pikachu', 15);
// Returns Collection of:
[
    'id' => 'xy1-42',
    'name' => 'Pikachu',
    'set_name' => 'XY',
    'img_small' => '/cards/xy1-42_small.png',
    'img_large' => '/cards/xy1-42_large.png',
    'ptcgo_code' => 'XY'
]
```

**Features:**
- Searches across `name`, `number_txt`, `ptcgo_code` fields
- Uses `MATCH ... AGAINST` in boolean mode
- Eager loads set relationship
- Limits results (default 15)
- Normalizes image paths

**Performance:**
- Full-text index faster than `LIKE` for large datasets
- Minimum 2-character query requirement
- Handles empty queries gracefully

### Use Cases

1. **Autocomplete Search**
   ```php
   // In controller
   public function performLookup(Request $request)
   {
       $query = $request->input('q', '');
       return $this->cardService->lookupCards($query);
   }
   ```

2. **Add Card to Collection Flow**
   - User types card name
   - Service returns matching cards
   - User selects specific card

---

## CollectionService

**File:** `app/Services/CollectionService.php`

Manages user collections with CRUD operations and authorization.

### Purpose
- Collection lifecycle management (create, read, update, delete)
- Default collection logic
- Public/private visibility control
- Authorization via Laravel Policies

### Dependencies
- `User` model
- `UserCollection` model
- Laravel `Gate` facade
- Laravel `DB` facade

### Public Methods

#### `createCollection(User $user, array $data): UserCollection`
Creates a new collection for a user.

```php
$collectionService->createCollection($user, [
    'name' => 'My Charizards',
    'description' => 'All Charizard cards',
    'is_public' => false,
    'is_default' => true
]);
```

**Logic:**
- Wraps in database transaction
- If `is_default = true`, sets all other user collections to non-default
- Ensures only one default per user

---

#### `getCollectionById(int $collectionId, ?User $requestingUser): UserCollection`
Retrieves collection with authorization check.

```php
$collectionService->getCollectionById(123, $currentUser);
```

**Authorization:**
- Uses Laravel Gate/Policy for access control
- Throws `ModelNotFoundException` if not found
- Throws `AuthorizationException` if unauthorized
- Supports guest access for public collections

---

#### `updateCollection(UserCollection $collection, array $data, User $requestingUser): UserCollection`
Updates collection attributes.

```php
$collectionService->updateCollection($collection, [
    'name' => 'Renamed Collection',
    'is_public' => true
], $user);
```

**Features:**
- Policy-based authorization
- Transaction-wrapped
- Handles `is_default` status changes
- Returns fresh model

---

#### `deleteCollection(UserCollection $collection, User $requestingUser): bool`
Deletes collection with ownership verification.

```php
$collectionService->deleteCollection($collection, $user);
```

**Logic:**
- Verifies ownership via policy
- If deleting default collection, promotes most recent to default
- Transaction-wrapped

---

#### `setCollectionAsDefault(UserCollection $collectionToMakeDefault, User $user): UserCollection`
Sets specific collection as user's default.

```php
$collectionService->setCollectionAsDefault($collection, $user);
```

**Ensures:**
- Other collections become non-default
- Transaction consistency

---

#### `setCollectionVisibility(UserCollection $collection, User $user, bool $isPublic): UserCollection`
Updates public/private status.

```php
$collectionService->setCollectionVisibility($collection, $user, true);
```

---

#### `getUserCollections(User $user, array $filters = []): LengthAwarePaginator`
Retrieves all collections for a user with optional filtering.

```php
$collectionService->getUserCollections($user, ['is_public' => true]);
// Returns paginated result (10 per page)
```

**Filters:**
- `is_public` - Filter by visibility

---

#### `getPublicCollections(array $filters = []): LengthAwarePaginator`
Lists all public collections across all users.

```php
$collectionService->getPublicCollections(['name_contains' => 'Charizard']);
// Returns paginated result (10 per page)
```

**Filters:**
- `name_contains` - Search by name

### Authorization Pattern

```php
// Uses Laravel Gate with policies
Gate::forUser($requestingUser)->authorize('update', $collection);
Gate::forUser($requestingUser)->authorize('delete', $collection);
Gate::forUser($requestingUser)->authorize('setDefault', $collection);
```

---

## CollectionItemService

**File:** `app/Services/CollectionItemService.php`

Manages collection items with support for variants, pricing, and complex queries.

### Purpose
- Item CRUD operations within collections
- Variant type resolution and pricing
- Advanced filtering, sorting, pagination
- Collection statistics calculation
- Duplicate detection and quantity management

### Dependencies
- `UserCollectionItem` model
- `Card` model
- `CardsVariant` model
- `CardsVariantType` model
- `CardsVariantsPricesMv` (materialized view)
- `CardsVariantsTypesMv` (materialized view)
- Laravel `DB` facade

### Public Methods

#### `getItemsForCollection(UserCollection $collection)`
Retrieves all items with optimized multi-table JOINs.

```php
$collectionItemService->getItemsForCollection($collection);
```

**Features:**
- Selects only necessary columns
- Joins cards, sets, variants, types tables
- Transforms flat result to nested objects
- Returns all items (no pagination)

---

#### `addItemToCollection(UserCollection $collection, array $data): UserCollectionItem`
Adds or increments a card in collection.

```php
$collectionItemService->addItemToCollection($collection, [
    'card_id' => 'xy1-1',
    'variant_id' => 12345,
    'variant_type' => 'normal',
    'condition' => 'near_mint',
    'language' => 'en',
    'quantity' => 1,
    'purchase_price' => 15.50
]);
```

**Smart Duplicate Handling:**
- Checks for existing entry with same unique constraint
- Constraint: card_id + variant_id + variant_type + condition + language + purchase_price
- If exists, increments quantity
- Otherwise creates new item
- Transaction-wrapped

---

#### `updateItemInCollection(UserCollection $collection, UserCollectionItem $item, array $data): UserCollectionItem`
Updates existing collection item.

```php
$collectionItemService->updateItemInCollection($collection, $item, [
    'quantity' => 3,
    'notes' => 'Graded PSA 10'
]);
```

---

#### `deleteItemFromCollection(UserCollection $collection, UserCollectionItem $item): bool`
Removes item from collection.

```php
$collectionItemService->deleteItemFromCollection($collection, $item);
```

---

#### `getTypesForCard(string $cardId)`
Retrieves all variant types available for a card.

```php
$collectionItemService->getTypesForCard('xy1-1');
// Returns: ['normal', 'reverse_holo', 'holofoil']
```

**Uses:** `CardsVariantsTypesMv` materialized view for performance

---

#### `resolveVariantForType(string $cardId, string $variantTypeCode)`
Looks up the specific variant (cm_id) for a card and variant type.

```php
$collectionItemService->resolveVariantForType('xy1-1', 'reverse_holo');
// Returns CardsVariant model
```

---

#### `getVariantPrices(int $variantId, ?int $variantTypeCode = null): ?array`
Fetches pricing data for a variant.

```php
$collectionItemService->getVariantPrices(12345, 'normal');
// Returns:
[
    'cardmarket' => [
        'low' => 5.50,
        'trend' => 8.00,
        'avg1' => 7.50,
        'avg7' => 7.80,
        'avg30' => 8.20
    ],
    'tcgplayer' => [
        'low' => 6.00,
        'trend' => 8.50
    ]
]
```

**Features:**
- Uses `CardsVariantsPricesMv` materialized view
- Distinguishes between normal and reverse holo pricing
- Returns null if variant not found

---

#### `getVariantDetails(int $variantId): ?array`
Comprehensive variant detail object.

```php
$collectionItemService->getVariantDetails(12345);
// Returns structured array with card info, variant type, all pricing
```

---

#### `mapCardVariantToCollectionItem(string $cardId, int $variantId, int $variantTypeCode): array`
Helper for add-to-collection flow.

```php
$collectionItemService->mapCardVariantToCollectionItem('xy1-1', 12345, 'normal');
// Returns collection item data structure with default quantity = 1
```

---

#### `getItemsForCollectionPaginated(UserCollection $collection, array $filters = [], string $sortBy = 'created_at', string $sortDirection = 'desc', int $perPage = 30)`
Advanced query with pagination, filtering, and sorting.

```php
$collectionItemService->getItemsForCollectionPaginated($collection, [
    'search' => 'Charizard',
    'condition' => 'near_mint',
    'language' => 'en',
    'rarity' => 'Rare Holo',
    'min_price' => 10,
    'max_price' => 100,
    'date_from' => '2024-01-01',
    'date_to' => '2024-12-31'
], 'name', 'asc', 30);
```

**Supported Filters:**
- `search` - Name or number search
- `condition` - Card condition
- `language` - Card language
- `rarity` - Card rarity
- `min_price` / `max_price` - Purchase price range
- `date_from` / `date_to` - Purchase date range

**Supported Sort Columns:**
- `created_at`, `name`, `number`, `rarity`
- `condition`, `purchase_price`, `quantity`, `market_price`

**Returns:** Paginated result with transformed items and market prices

---

#### `getCollectionStats(UserCollection $collection): array`
Calculates collection summary statistics.

```php
$collectionItemService->getCollectionStats($collection);
// Returns:
[
    'total_cards' => 150,        // Sum of quantities
    'unique_cards' => 45,        // Distinct card_id count
    'total_purchase_value' => 1250.50,
    'total_market_value' => 1450.75,
    'value_difference' => 200.25
]
```

**Use Case:** Dashboard displays, collection overview

---

#### `getFilterOptions(UserCollection $collection): array`
Returns available dropdown options for filters.

```php
$collectionItemService->getFilterOptions($collection);
// Returns:
[
    'rarities' => ['Common', 'Uncommon', 'Rare Holo'],
    'languages' => ['en', 'jp', 'de'],
    'conditions' => ['mint', 'near_mint', 'excellent']
]
```

**Use Case:** Populate filter UI dropdowns with collection-specific options

### Performance Features

1. **Materialized Views**: Heavy queries use pre-computed views
2. **Selective Columns**: Avoid `SELECT *`
3. **JOIN Optimization**: Multi-table joins in single query
4. **Transaction Safety**: All mutations wrapped in transactions
5. **Smart Increment**: Prevents duplicate entries automatically

---

## Service Architecture Patterns

### 1. Dependency Injection
```php
// In Controller
public function __construct(
    private CollectionService $collectionService,
    private CollectionItemService $collectionItemService
) {}
```

### 2. Transaction Pattern
```php
return DB::transaction(function () use ($data) {
    // Multiple database operations
    // Automatic rollback on failure
});
```

### 3. Authorization Pattern
```php
Gate::forUser($user)->authorize('action', $model);
// Throws AuthorizationException if denied
```

### 4. Caching Pattern
```php
Cache::rememberForever($key, function () {
    // Expensive operation
});
```

### 5. Data Transformation Pattern
```php
// Flat DB result → Nested object structure
$result->card = (object)[
    'id' => $row->card_id,
    'name' => $row->card_name,
    'set' => (object)[
        'name' => $row->set_name
    ]
];
```

---

## Integration Points

### Controller Usage
```php
class CollectionController extends Controller
{
    public function __construct(
        private CollectionService $collectionService
    ) {}

    public function store(CollectionStoreRequest $request)
    {
        $collection = $this->collectionService->createCollection(
            $request->user(),
            $request->validated()
        );
        return redirect()->route('collections.show', $collection);
    }
}
```

### Route Mapping
- **catalog.php** → CardService, CardImageService
- **collections.php** → CollectionService, CollectionItemService
- **profile.php** → CollectionService (user's collections)

---

## Testing Considerations

Services are designed for testability:

1. **No Direct HTTP Access**: Pure business logic
2. **Dependency Injection**: Easy to mock
3. **Transaction Support**: Database state management
4. **Return Types**: Predictable return values
5. **Stateless**: No internal state between calls (except caching)

```php
// Example test
public function test_creates_collection_with_default_flag()
{
    $service = new CollectionService();
    $user = User::factory()->create();

    $collection = $service->createCollection($user, [
        'name' => 'Test',
        'is_default' => true
    ]);

    $this->assertTrue($collection->is_default);
}
```
