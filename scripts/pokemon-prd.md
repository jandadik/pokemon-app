# PRD: Modul pro správu sbírky Pokémon karet

## Přehled

Tento dokument popisuje rozšíření existující Pokémon aplikace o modul pro správu sbírky Pokémon karet. Modul umožní uživatelům evidovat své sbírky karet, sledovat kompletaci setů a řídit nákup chybějících karet. Řešení bude plně integrováno s existující aplikací, která již obsahuje databázi karet, setů a katalogových informací.

Systém je určen pro sběratele Pokémon karet od začátečníků po pokročilé, kteří chtějí svou sbírku systematicky spravovat a mít přehled o její hodnotě a kompletnosti. Hlavní přidanou hodnotou je možnost sledovat kompletaci setů z různých pohledů (např. základní, master, reverzní holo), což jiné aplikace typicky nepodporují v takové míře.

## Existující infrastruktura

Aplikace již obsahuje:
- Databázové schéma a modely pro správu sbírky (user_collections, user_collection_items, user_set_tracking, user_set_cards, atd.)
- Katalog karet a setů importovaný z Pokémon API
- Cenové informace z TCGPlayer a CardMarket
- Základní autentizační systém s rolemi a oprávněními
- Laravel 12 backend s Vue.js 3 a Inertia.js na frontendu
- Lokalizační systém postavený na Vue-i18n

Všechny potřebné databázové tabulky již existují včetně Laravel migrací a modelů. Schéma databáze je definováno v souboru pokemon_lara.sql.

## Klíčové funkce

### 1. Správa sbírek

**Co to dělá:**
- Uživatelé mohou vytvářet, upravovat a mazat sbírky karet
- Každý uživatel může mít více sbírek pro různé účely (hlavní sbírka, karty na prodej, investiční karty)
- Jedna sbírka je vždy označena jako výchozí
- Sbírky mohou být veřejné nebo soukromé

**Proč je to důležité:**
- Umožňuje organizaci karet podle různých kritérií a účelů
- Nabízí flexibilitu pro různé strategie sběratelství

**Jak to funguje:**
- Uživatel vytvoří sbírku s názvem a popisem
- Systém automaticky vytvoří záznam v `user_collections`
- Uživatel může označit sbírku jako výchozí nebo veřejnou

### 2. Evidence karet ve sbírce

**Co to dělá:**
- Přidávání karet do sbírky včetně detailních informací
- Evidence stavu karty (mint, near mint, atd.)
- Sledování jazyka, foil/non-foil statusu, first edition
- Evidence gradeovaných karet včetně společnosti a hodnoty
- Zaznamenávání nákupní ceny, data a umístění

**Proč je to důležité:**
- Přesná evidence zvyšuje hodnotu sbírky
- Detailní informace jsou klíčové pro obchodování a pojištění

**Jak to funguje:**
- Uživatel vyhledá kartu v katalogu
- Vybere variantu karty a vyplní detaily
- Systém uloží záznam do `user_collection_items`
- Automaticky probíhá propojení s případným sledováním kompletace setů

### 3. Sledování kompletace setů

**Co to dělá:**
- Uživatel si může vybrat sety, které chce kompletovat
- Pro každý set lze zvolit pohled na kompletaci (base, master, standard, parallel, atd.)
- Systém vytváří přehled vlastněných a chybějících karet
- Vizualizace postupu kompletace setů

**Proč je to důležité:**
- Poskytuje jasný přehled o postupu kompletace
- Různé pohledy umožňují sledovat specifické varianty karet
- Umožňuje efektivně plánovat nákupy chybějících karet

**Jak to funguje:**
- Uživatel vybere set a pohled na set
- Systém vytvoří záznam v `user_set_tracking`
- Na základě pravidel v `set_tracking_rules` se vytvoří záznamy v `user_set_cards`
- Propojení s kartami ve sbírce probíhá automaticky

### 4. Sledování hodnoty sbírky

**Co to dělá:**
- Systém sleduje aktuální tržní hodnotu karet ve sbírce
- Ukládá denní snímky hodnoty sbírky
- Zobrazuje vývoj hodnoty v čase
- Vypočítává ROI (návratnost investice)

**Proč je to důležité:**
- Umožňuje sledovat investici do sbírky
- Identifikuje růst nebo pokles hodnoty
- Poskytuje data pro investiční rozhodování

**Jak to funguje:**
- Systém denně aktualizuje hodnoty karet z externích zdrojů
- Ukládá snímky celkové hodnoty do `user_collection_prices`
- Generuje grafy vývoje hodnoty v čase

### 5. Wishlist a nákupní plánování

**Co to dělá:**
- Uživatelé mohou vytvářet seznam karet, které chtějí získat
- Nastavení priority a maximální kupní ceny
- Propojení s chybějícími kartami v setech
- Automatické upozornění na karty, jejichž cena klesla pod stanovený limit

**Proč je to důležité:**
- Pomáhá plánovat nákupy a rozpočet
- Zabraňuje impulzivním nákupům
- Optimalizuje kompletaci sbírky

**Jak to funguje:**
- Uživatel přidává karty na wishlist ručně nebo z přehledu chybějících karet
- Systém ukládá záznamy do `user_wishlists`
- Pravidelně kontroluje ceny a upozorňuje na příležitosti k nákupu

## Uživatelská zkušenost

### Klíčové uživatelské scénáře

#### Založení sledování setu
1. Uživatel přejde do sekce "Sety"
2. Vyhledá a vybere set, který chce sledovat
3. Zvolí pohled na set (base, master, standard)
4. Nastaví prioritu a cíle
5. Systém vytvoří sledování a zobrazí karty

#### Přidání karty do sbírky
1. Uživatel vyhledá kartu v katalogu
2. Zvolí variantu karty (normal, holo, reverse holo)
3. Vyplní detaily (stav, jazyk, cena)
4. Vybere sbírku, do které se karta přidá
5. Systém automaticky aktualizuje status v sledování setů

#### Analýza hodnoty sbírky
1. Uživatel přejde do přehledu sbírky
2. Zobrazí statistiky a grafy hodnoty
3. Může filtrovat podle různých kritérií (set, rarita, atd.)
4. Zobrazí ROI a trendy hodnoty v čase

### Persony

#### Začínající sběratel
- Potřebuje jednoduché UI pro správu menší sbírky
- Zajímá se především o kompletaci jednoho nebo několika málo setů
- Nemá zkušenosti s hodnotou karet a variantami

#### Pokročilý sběratel
- Má rozsáhlou sbírku s mnoha sety
- Používá více sbírek pro různé účely
- Potřebuje pokročilé funkce pro správu variant a kompletaci
- Zajímá se o hodnotu sbírky a její vývoj

#### Investor
- Primárně sleduje hodnotu karet
- Potřebuje detailní statistiky a cenové trendy
- Využívá pokročilé filtry pro analýzu sbírky
- Zaměřuje se na specifické rarities a varianty

### UI/UX principy

- Responzivní design (desktop, tablet, mobil)
- Intuitivní navigace mezi sbírkou a sledováním setů
- Vizuální indikátory postupu kompletace
- Jasné barevné rozlišení variant karet
- Hierarchický přístup k informacím (od celkového přehledu k detailům)
- Konzistentní použití komponent z Vuetify 3.7.12

## Technická architektura

### Systémové komponenty

#### Backend (Laravel 12)
- Controllers:
  - `CollectionController` - CRUD operace pro sbírky
  - `CollectionItemController` - správa karet ve sbírce
  - `SetTrackingController` - sledování kompletace setů
  - `WishlistController` - správa wishlistu
  - `CollectionAnalysisController` - analýza hodnoty sbírky

- Services:
  - `CollectionService` - business logika pro sbírky
  - `SetCompletionService` - logika pro sledování kompletace
  - `CardVariantMappingService` - mapování variant karet
  - `PriceTrackingService` - sledování cen a historie
  - `ImportExportService` - import/export dat

- Jobs:
  - `UpdateCollectionValueJob` - aktualizace hodnoty sbírky
  - `CheckWishlistPricesJob` - kontrola cen pro wishlist
  - `SyncCollectionWithSetTrackingJob` - synchronizace sbírky a sledování

#### Frontend (Vue.js 3 s Inertia.js)
- Stránky:
  - `Collections/Index.vue` - přehled sbírek
  - `Collections/Show.vue` - detail sbírky
  - `Collections/Edit.vue` - úprava sbírky
  - `SetTracking/Index.vue` - přehled sledovaných setů
  - `SetTracking/Show.vue` - detail sledování setu
  - `Wishlist/Index.vue` - správa wishlistu
  - `Analysis/Dashboard.vue` - analýza hodnoty

- Komponenty:
  - `CollectionStats.vue` - statistiky sbírky
  - `SetCompletionHeatmap.vue` - vizualizace kompletace
  - `CardVariantSelector.vue` - výběr variant karty
  - `PriceHistoryChart.vue` - graf historie cen
  - `CollectionValueChart.vue` - graf hodnoty sbírky

### Datové modely

Všechny tabulky již existují v databázi, včetně Laravel migrací a modelů:

- **User Collections**:
  - `user_collections` - tabulka sbírek uživatelů
  - `user_collection_items` - karty ve sbírkách
  - `user_collection_prices` - historie hodnoty sbírky

- **Set Tracking**:
  - `user_set_tracking` - sledování kompletace setů
  - `user_set_cards` - karty v rámci sledování setu
  - `set_tracking_rules` - pravidla pro definici pohledů na sety

- **Wishlist**:
  - `user_wishlists` - seznam chtěných karet

- **Katalog**:
  - `cards` - informace o kartách
  - `cards_variant` - varianty karet
  - `sets` - informace o setech
  - `prices_tcg` a `prices_cm` - cenové informace

### Lokalizace

- Vue-i18n pro frontend lokalizaci
- Překlady uložené v `resources/lang/` adresářích
- Použití `$t()` metody ve Vue komponentách
- Podpora pro více jazyků (čeština, angličtina)

### Infrastrukturní požadavky

- MariaDB/MySQL pro databázi
- Redis pro cache a fronty
- Plánovač úloh (Laravel Scheduler) pro pravidelné aktualizace
- Queue Worker pro zpracování asynchronních úloh

## Plán vývoje

### MVP (Minimum Viable Product)

#### Základní správa sbírky
- CRUD operace pro sbírky
- Přidávání/odebírání karet ze sbírky
- Základní detaily karet (stav, jazyk, varianta)
- Jednoduchý filtr a vyhledávání
- Přehled sbírky s počty karet

#### Jednoduché sledování setů
- Výběr setu pro sledování
- Základní pohled na kompletaci (base set)
- Manuální označení vlastněných karet
- Jednoduché statistiky kompletace

#### Primitivní wishlist
- Přidávání karet na wishlist
- Jednoduchý seznam chtěných karet
- Filtry dle priority

#### UI/UX
- Responzivní layout pro desktop a mobil
- Základní statistiky na dashboardu
- Seznam sbírek a setů

### Fáze 2: Rozšířené funkce

#### Pokročilá správa sbírky
- Podpora všech variant karet
- Detailní informace o kartách (grading, lokace, atd.)
- Hromadné operace
- Import/export dat (CSV, Excel)

#### Komplexní sledování setů
- Podpora všech typů pohledů na sety
- Automatická synchronizace se sbírkou
- Vizualizace kompletace pomocí heatmapy
- Detailní statistiky a reporty

#### Rozšířený wishlist
- Automatické generování z chybějících karet
- Cenové limity a upozornění
- Prioritizace karet
- Propojení s externími obchody

#### Sledování hodnoty
- Denní aktualizace hodnoty sbírky
- Grafy vývoje hodnoty v čase
- Výpočet ROI a statistiky

#### UI/UX vylepšení
- Pokročilé filtry a vyhledávání
- Intuitivní rozhraní pro správu variant
- Dashboardy s klíčovými metrikami
- Sdílení sbírek a statistik

### Fáze 3: Pokročilá funkcionalita

#### Analýza sbírky
- Pokročilé analytické nástroje
- Predikce růstu hodnoty
- Doporučení pro investice
- Porovnání s tržními trendy

#### Sociální funkce
- Sdílení sbírek a statistik
- Porovnání s ostatními sběrateli
- Hledání partnerů pro výměnu
- Komunita a diskuze

#### Marketplace integrace
- Propojení s externími marketplace
- Automatické hledání nejlepších nabídek
- Alert systém pro wishlist
- Podpora pro prodej a výměnu

#### Mobile-first přístup
- Nativní mobilní zkušenost
- Offline přístup k datům
- Skenování karet pomocí fotoaparátu
- Push notifikace pro ceny a wishlist

#### Automatizace
- AI asistent pro kompletaci
- Automatické doporučení nákupů
- Smart import pomocí obrázků
- Automatické generování reportů

## Logická závislostní struktura

### Základ (fáze 1)
1. CRUD operace pro sbírky (nutný základ pro vše ostatní)
2. Přidávání/odebírání karet (nutné pro vše související s kartami)
3. Základní sledování setů (navazuje na správu karet)
4. Jednoduchý wishlist (navazuje na katalog karet)
5. Základní UI pro prohlížení a správu (nutné pro použitelnost)

### Rozšíření (fáze 2)
1. Pokročilá správa variant karet (rozšiřuje základní správu karet)
2. Komplexní sledování setů (rozšiřuje základní sledování)
3. Sledování hodnoty sbírky (vyžaduje správu karet a ceny)
4. Rozšířený wishlist (vyžaduje základní wishlist)
5. Pokročilé UI/UX (vylepšuje základní UI)

### Pokročilé funkce (fáze 3)
1. Analytické nástroje (vyžaduje data z fáze 2)
2. Sociální funkce (vyžaduje základní sdílení)
3. Marketplace integrace (vyžaduje rozšířený wishlist)
4. Mobilní vylepšení (rozšiřuje existující UI)
5. Automatizace a AI (vyžaduje robustní datový základ)

## Rizika a mitigace

### Technická rizika

#### Výkon při velkých sbírkách
- **Riziko:** Zpomalení systému u uživatelů s tisíci karet
- **Mitigace:** Implementace paginace, lazy loading, optimalizace dotazů, denormalizace kritických dat

#### Synchronizace dat
- **Riziko:** Nekonzistence mezi sbírkou a sledováním setů
- **Mitigace:** Transakční zpracování, automatické synchronizační joby, validace integrity

#### Aktualizace cen
- **Riziko:** Problémy s externími API, neaktuální ceny
- **Mitigace:** Fallback mechanismy, cache strategie, postupná degradace funkcionality

### Produktová rizika

#### Komplexnost UI
- **Riziko:** Příliš komplexní rozhraní pro začátečníky
- **Mitigace:** Vrstvený přístup k funkcím, progressive disclosure, onboarding průvodce

#### Správné mapování variant
- **Riziko:** Nepřesné mapování variant karet mezi systémy
- **Mitigace:** Detailní validace, manuální kontroly, zpětná vazba od uživatelů

#### Prioritizace funkcí
- **Riziko:** Zaměření na špatné funkce, které uživatelé nevyužijí
- **Mitigace:** Validace s komunitou, A/B testování, sledování využití funkcí

## Příloha

### Technické specifikace

#### Databázové vztahy
- User má mnoho Collections
- Collection má mnoho CollectionItems
- User má mnoho SetTrackings
- SetTracking má mnoho SetCards
- CollectionItem může plnit požadavek na SetCard

#### Klíčové routy
- `/collections` - seznam sbírek
- `/collections/{id}` - detail sbírky
- `/collections/{id}/items` - karty ve sbírce
- `/set-tracking` - přehled sledovaných setů
- `/set-tracking/{id}` - detail sledování setu
- `/wishlist` - správa wishlistu

#### Routing
- Všechny webové routy budou definovány v souborech v adresáři `routes/`
- Hlavní soubory: `routes/collections.php`, `routes/set-tracking.php`, `routes/wishlist.php`
- Všechny routy budou mít přiřazené jméno pro použití v `route()` helperu
- Middleware pro autentizaci a autorizaci bude aplikován na úrovni skupin rout

### Výzkumné poznatky

#### Konkurenční analýza
- Většina existujících aplikací nepodporuje různé pohledy na kompletaci setů
- Chybí propojení mezi sledováním setů a správou sbírky
- Nedostatečná podpora pro různé varianty karet (holo, reverse holo, atd.)

#### Uživatelský výzkum
- Sběratelé preferují vizuální přehled o kompletaci
- Hodnota sbírky je klíčovou metrikou pro většinu pokročilých sběratelů
- Začátečníci potřebují jednoduché rozhraní a postupné odkrývání komplexity

#### Technické analýzy
- Vue.js s Inertia.js poskytuje optimální kompromis mezi SPA a tradičním přístupem
- Laravel 12 nabízí výkon a flexibilitu pro budoucí rozšíření
- MariaDB škáluje dobře pro předpokládané objemy dat
