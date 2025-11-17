# Laravel Models Documentation

This document provides comprehensive documentation of all Eloquent models in the Pokemon Card Collection application.

## Model Inventory Summary

| Category | Models | Count |
|----------|--------|-------|
| **Core Card Data** | Card, Set, Attack, CardsVariant, CardsVariantType | 5 |
| **Pricing** | PricesTcg, PricesCm | 2 |
| **Materialized Views** | CardsPricesMv, CardsVariantsTypesMv, CardsVariantsPricesMv | 3 |
| **User Management** | User, UserParameter, Session, LoginHistory | 4 |
| **Collections** | UserCollection, UserCollectionItem, UserCollectionPrice | 3 |
| **Set Tracking** | UserSetTracking, UserSetCard, SetTrackingRule | 3 |
| **Wishlist** | UserWishlist | 1 |
| **System Config** | Register, RegisterCategory | 2 |
| **Total** | - | **24** |

---

## Complete Relationship Diagram

```
User
├── hasOne(UserParameter)
├── hasMany(Session)
├── hasMany(LoginHistory)
├── hasMany(UserCollection)
│   ├── hasMany(UserCollectionItem)
│   │   ├── belongsTo(Card)
│   │   ├── belongsTo(CardsVariant)
│   │   └── belongsTo(CardsVariantType)
│   ├── hasMany(UserCollectionPrice)
│   └── hasMany(UserSetTracking)
├── hasMany(UserSetCard)
├── hasMany(UserSetTracking)
└── hasMany(UserWishlist)

Set
└── hasMany(Card)
    ├── hasMany(Attack)
    ├── hasMany(CardsVariant)
    │   └── hasMany(PricesCm)
    └── hasMany(PricesTcg)

RegisterCategory
└── hasMany(Register)
```

---

## Core Card Models

### 1. Card
**File:** `app/Models/Card.php`

The central model representing a Pokemon card.

**Primary Key:** String (Pokemon TCG API ID), not auto-incrementing

**Fillable Attributes:**
```php
'set_id', 'name', 'supertype', 'number', 'ptcgo_code',
'types', 'subtypes', 'rules', 'rarity', 'regulation_mark',
'hp', 'national_pokedex_number', 'evolves_from', 'evolves_to',
'abilities', 'weaknesses', 'resistances', 'retreat_cost', 'converted_retreat_cost',
'ancient_trait', 'flavor_text', 'illustrator', 'legalities',
'img_small', 'img_file_small', 'img_large', 'img_file_large',
'url_tcgplayer', 'url_cardmarket'
```

**Array Casts:**
- `types`, `subtypes`, `rules`, `evolves_to`, `abilities`
- `weaknesses`, `resistances`, `retreat_cost`, `legalities`

**Relationships:**
```php
public function set(): BelongsTo
public function attacks(): HasMany
public function variants(): HasMany  // CardsVariant
public function pricesTcg(): HasMany
```

**Traits:** `HasFactory`

---

### 2. Set
**File:** `app/Models/Set.php`

Pokemon card set/expansion.

**Primary Key:** String (Pokemon TCG API ID), not auto-incrementing

**Fillable Attributes:**
```php
'name', 'series', 'printed_total', 'total',
'ptcgo_code', 'release_date', 'symbol_url', 'logo_url'
```

**Casts:**
- `release_date` → datetime

**Relationships:**
```php
public function cards(): HasMany
```

**Custom Methods:**
```php
public function getMarketPrice()
// Calculates total market value of all cards in set
// Sum of avg30 + reverse_holo_avg30 from pricing data
```

**Traits:** `HasFactory`

---

### 3. Attack
**File:** `app/Models/Attack.php`

Pokemon card attacks.

**Timestamps:** Disabled

**Fillable Attributes:**
```php
'card_id', 'name', 'cost', 'damage', 'text', 'converted_energy_cost'
```

**Relationships:**
```php
public function card(): BelongsTo
```

---

### 4. CardsVariant
**File:** `app/Models/CardsVariant.php`

Card printing variants (holo, reverse holo, promo, etc.).

**Primary Key:** `cm_id` (CardMarket ID), integer

**Timestamps:** Disabled

**Fillable Attributes:**
```php
'card_id', 'cm_expansion_id', 'cm_metacard_id', 'date_added',
'collector_number', 'ptcgo_code', 'tcgplayer_id', 'rarity',
'variant_normal', 'variant_holo', 'variant_reverse', 'variant_promo',
'variant', 'variant_pokeball', 'variant_masterball'
```

**Boolean Casts:**
- `variant_normal`, `variant_holo`, `variant_reverse`, `variant_promo`
- `variant_pokeball`, `variant_masterball`

**Relationships:**
```php
public function card(): BelongsTo
public function pricesCm(): HasMany
```

---

### 5. CardsVariantType
**File:** `app/Models/CardsVariantType.php`

Variant type definitions (lookup table).

**Timestamps:** Disabled

**Fillable Attributes:**
```php
'code', 'variant', 'name', 'price_column_suffix', 'description'
```

**Traits:** `HasFactory`

---

## Pricing Models

### 6. PricesTcg
**File:** `app/Models/PricesTcg.php`

TCGPlayer pricing data per card.

**Timestamps:** Disabled

**Fillable Attributes:**
```php
'card_id', 'price_type', 'price_low', 'price_mid',
'price_high', 'price_market', 'price_direct_low'
```

**Float Casts:** All price fields

**Relationships:**
```php
public function card(): BelongsTo
```

---

### 7. PricesCm
**File:** `app/Models/PricesCm.php`

CardMarket pricing data per variant.

**Primary Key:** `cm_id` (not auto-incrementing)

**Timestamps:** Disabled

**Fillable Attributes:**
```php
'card_id', 'average_sell_price', 'low_price', 'trend_price',
'german_pro_low', 'suggested_price', 'reverse_holo_sell', 'reverse_holo_low',
'reverse_holo_trend', 'low_price_ex_plus', 'avg1', 'avg7', 'avg30',
'reverse_holo_avg1', 'reverse_holo_avg7', 'reverse_holo_avg30'
```

**Float Casts:** All price fields

**Relationships:**
```php
public function variant(): BelongsTo  // CardsVariant via cm_id
```

---

## Materialized View Models

These models represent pre-computed database views (read-only).

### 8. CardsPricesMv
**File:** `app/Models/CardsPricesMv.php`

Aggregated card pricing view.

**Primary Key:** `card_id`

**Timestamps:** Disabled

**Relationships:**
```php
public function card(): BelongsTo
public function set(): BelongsTo
public function variant(): BelongsTo  // Optional CardsVariant
```

---

### 9. CardsVariantsTypesMv
**File:** `app/Models/CardsVariantsTypesMv.php`

Variants with type information - most sophisticated materialized view model.

**Primary Key:** `mv_id`

**Timestamps:** Disabled

**Guarded:** All attributes (read-only)

**Traits:** `HasFactory`

**Relationships:**
```php
public function card(): BelongsTo
public function variant(): BelongsTo  // CardsVariant
```

**Query Scopes:**
```php
public function scopeForCard($query, string $cardId)
public function scopePrimaryVariants($query)
public function scopeVariantType($query, $variantTypeCode)
public function scopeWithPricing($query)
```

**Static Methods:**
```php
public static function getVariantTypesForCard(string $cardId)
// Returns available variant types, cached 5 minutes

public static function resolveVariantForType(string $cardId, string $variantTypeCode)
// Finds specific variant for card and type

public static function getPrimaryVariantForCard(string $cardId)
// Gets primary (default) variant

public static function getVariantsForCards(array $cardIds)
// Batch loads variants for multiple cards

public static function getStats()
// Returns materialized view statistics

public static function getCompleteVariantDetails(string $cardId, string $variantTypeCode)
// Complete variant info with caching (10 min)
```

**Accessor Attributes:**
```php
public function getFormattedVariantTypeAttribute()
public function getVariantIdAttribute()  // Alias for cm_id
public function getIsReverseHoloAttribute()
```

---

### 10. CardsVariantsPricesMv
**File:** `app/Models/CardsVariantsPricesMv.php`

Variant-specific pricing view.

**Primary Key:** `cm_id`

**Timestamps:** Disabled

**Decimal Casts:** Extensive price field casts

**Relationships:**
```php
public function variant(): BelongsTo  // CardsVariant
public function card(): BelongsTo
```

---

## User Models

### 11. User
**File:** `app/Models/User.php`

Core user model with authentication.

**Extends:** `Authenticatable`

**Fillable Attributes:**
```php
'name', 'email', 'password', 'phone', 'bio', 'settings'
```

**Hidden Attributes:**
```php
'password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'
```

**Casts:**
- `email_verified_at` → datetime
- `password` → hashed
- `two_factor_enabled` → boolean
- `settings` → array

**Relationships:**
```php
public function parameters(): HasOne  // UserParameter
public function sessions(): HasMany
public function loginHistory(): HasMany
public function collections(): HasMany  // UserCollection
```

**Traits:** `HasFactory`, `Notifiable`, `HasRoles` (Spatie Permission)

---

### 12. UserParameter
**File:** `app/Models/UserParameter.php`

User settings and preferences.

**Fillable Attributes:**
```php
'user_id', 'settings'
```

**Relationships:**
```php
public function user(): BelongsTo
```

---

### 13. Session
**File:** `app/Models/Session.php`

Active user sessions.

**Primary Key:** String (session ID), not auto-incrementing

**Timestamps:** Disabled

**Fillable Attributes:**
```php
'id', 'user_id', 'ip_address', 'user_agent', 'payload', 'last_activity'
```

**Casts:**
- `last_activity` → datetime

**Relationships:**
```php
public function user(): BelongsTo
```

---

### 14. LoginHistory
**File:** `app/Models/LoginHistory.php`

User login history and security tracking.

**Fillable Attributes:**
```php
'user_id', 'ip_address', 'user_agent', 'location',
'city', 'country', 'is_suspicious', 'notified', 'status'
```

**Boolean Casts:** `is_suspicious`, `notified`

**Relationships:**
```php
public function user(): BelongsTo
```

**Query Scopes:**
```php
public function scopeSuspicious($query)
public function scopeNotNotified($query)
public function scopeWithStatus($query, $status)
```

**Instance Methods:**
```php
public function isSuspicious(): bool
public function markAsNotified(): void
```

**Traits:** `HasFactory`

---

## Collection Models

### 15. UserCollection
**File:** `app/Models/UserCollection.php`

User's card collection container.

**Fillable Attributes:**
```php
'user_id', 'name', 'description', 'is_public', 'is_default'
```

**Boolean Casts:** `is_public`, `is_default`

**Relationships:**
```php
public function user(): BelongsTo
public function items(): HasMany  // UserCollectionItem
public function prices(): HasMany  // UserCollectionPrice
public function setTrackings(): HasMany  // UserSetTracking
```

**Traits:** `HasFactory`

---

### 16. UserCollectionItem
**File:** `app/Models/UserCollectionItem.php`

Individual card entry in a collection.

**Fillable Attributes:**
```php
'collection_id', 'card_id', 'variant_id', 'variant_type',
'quantity', 'condition', 'language', 'is_first_edition', 'is_graded',
'grade_company', 'grade_value', 'purchase_price', 'purchase_date',
'notes', 'location'
```

**Casts:**
- `is_first_edition`, `is_graded` → boolean
- `purchase_price` → decimal:2
- `purchase_date` → date

**Relationships:**
```php
public function collection(): BelongsTo  // UserCollection
public function card(): BelongsTo
public function variant(): BelongsTo  // CardsVariant
public function variantType(): BelongsTo  // CardsVariantType
public function setCard(): HasOne  // UserSetCard
```

**Traits:** `HasFactory`

---

### 17. UserCollectionPrice
**File:** `app/Models/UserCollectionPrice.php`

Historical price snapshots for collections.

**Fillable Attributes:**
```php
'collection_id', 'total_market_value', 'total_purchase_value',
'total_cards', 'snapshot_date'
```

**Casts:**
- `total_market_value`, `total_purchase_value` → decimal:2
- `total_cards` → integer
- `snapshot_date` → date

**Relationships:**
```php
public function collection(): BelongsTo  // UserCollection
```

**Traits:** `HasFactory`

---

## Set Tracking Models

### 18. UserSetTracking
**File:** `app/Models/UserSetTracking.php`

User's set completion tracking.

**Fillable Attributes:**
```php
'user_id', 'set_id', 'collection_id', 'tracking_view',
'priority', 'status'
```

**Relationships:**
```php
public function user(): BelongsTo
public function set(): BelongsTo
public function collection(): BelongsTo  // UserCollection (optional)
public function cards(): HasMany  // UserSetCard (composite key)
```

**Traits:** `HasFactory`

**Notes:** Uses composite key relationship (user_id, set_id, tracking_view)

---

### 19. UserSetCard
**File:** `app/Models/UserSetCard.php`

Individual card tracking within a set.

**Fillable Attributes:**
```php
'user_id', 'set_id', 'card_id', 'variant_id',
'tracking_view', 'status', 'collection_item_id'
```

**Relationships:**
```php
public function user(): BelongsTo
public function set(): BelongsTo
public function card(): BelongsTo
public function variant(): BelongsTo  // CardsVariant
public function collectionItem(): BelongsTo  // UserCollectionItem
public function tracking(): BelongsTo  // UserSetTracking (composite key)
```

**Traits:** `HasFactory`

**Notes:** Uses composite key relationship (user_id, set_id, tracking_view)

---

### 20. SetTrackingRule
**File:** `app/Models/SetTrackingRule.php`

Configuration rules for set tracking.

**Fillable Attributes:**
```php
'tracking_view', 'rarity', 'variant_type',
'series_from', 'series_to', 'include_in_printed_range', 'include_above_printed'
```

**Boolean Casts:** `include_in_printed_range`, `include_above_printed`

**Traits:** `HasFactory`

---

## Wishlist Model

### 21. UserWishlist
**File:** `app/Models/UserWishlist.php`

User's card wishlist.

**Fillable Attributes:**
```php
'user_id', 'card_id', 'variant_id',
'priority', 'target_condition', 'max_price', 'notes'
```

**Casts:**
- `max_price` → decimal:2

**Relationships:**
```php
public function user(): BelongsTo
public function card(): BelongsTo
public function variant(): BelongsTo  // CardsVariant
```

**Traits:** `HasFactory`

---

## System Configuration Models

### 22. RegisterCategory
**File:** `app/Models/RegisterCategory.php`

Categories for system registers.

**Fillable Attributes:**
```php
'name', 'type'
```

**Relationships:**
```php
public function registers(): HasMany
```

---

### 23. Register
**File:** `app/Models/Register.php`

System register/lookup values.

**Fillable Attributes:**
```php
'register_category_id', 'name', 'type', 'default'
```

**Boolean Casts:** `default`

**Relationships:**
```php
public function category(): BelongsTo  // RegisterCategory
```

---

## Key Architecture Notes

1. **String Primary Keys**: Card, Set use string IDs from Pokemon TCG API
2. **Composite Keys**: UserSetTracking and UserSetCard use composite keys
3. **Materialized Views**: 3 read-only models for performance optimization
4. **Pricing Sources**: Dual pricing (TCGPlayer + CardMarket)
5. **Soft Deletes**: Not used in any models
6. **Timestamps**: Disabled for static data (cards, prices, variants)
7. **Factory Support**: 11 models have HasFactory trait for testing
8. **Authorization**: User model uses Spatie HasRoles trait
