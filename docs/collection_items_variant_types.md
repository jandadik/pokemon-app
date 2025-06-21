# Evidence typů a variant karet v uživatelské sbírce

## Kontext a důvod

V systému pro správu sbírek karet je potřeba umožnit uživateli evidovat nejen konkrétní variantu karty (např. jazyk, edice, foil), ale i tzv. typ karty (např. regular, reverse holo, promo). Důvodem je, že jedna varianta v tabulce `cards_variant` může odpovídat více typům karet. Proto je nutné evidovat jak konkrétní variantu, tak i typ, který uživatel zvolil.

## Základní entity a tabulky
- **cards** – základní seznam všech karet (např. „Pikachu“)
- **cards_variant** – konkrétní varianty karet (např. Pikachu, 1st Edition, Foil, různé jazyky atd.), klíčové pole `cm_id`
- **cards_variant_types** – typy karet, které nabízíme uživateli pro evidenci (např. „regular“, „reverse holo“, „promo“)
- **user_collection_items** – položky ve sbírce uživatele, nutno rozšířit o pole `variant_type` (kód typu z `cards_variant_types`)

## Výběr typu karty pro uživatele
- Uživatel při přidávání karty do sbírky vybírá typ karty z tabulky `cards_variant_types`, nikoliv přímo variantu z `cards_variant`.
- Seznam typů karet pro konkrétní kartu získáme pomocí "trojvazby":
    - `cards.card_id = cards_variant.card`
    - `cards_variant.variant = cards_variant_types.variant`
- Výsledkem je seznam typů (`cards_variant_types`), které jsou relevantní pro danou kartu.

## Uložení do sbírky
- Po výběru typu karty uživatelem:
    - Pomocí `card_id` a `variant` z `cards_variant_types` jednoznačně určujeme konkrétní variantu (`cm_id`) v tabulce `cards_variant`.
    - Do tabulky `user_collection_items` ukládáme:
        - `card_id`
        - `variant` (odkaz na `cards_variant.cm_id`)
        - `variant_type` (kód typu z `cards_variant_types`)
- Důvod: Jedna varianta (`cm_id`) může odpovídat více typům karet (např. „regular“ i „reverse holo“), proto je nutné evidovat i typ (`variant_type`).

## Praktická logika pro backend

### a) Získání typů karet pro konkrétní kartu
- Pro dané `card_id`:
    1. Najít všechny záznamy v `cards_variant`, které na ni patří.
    2. Pro všechny tyto varianty najít odpovídající typy v `cards_variant_types` (pomocí pole `variant`).
    3. Výsledkem je seznam typů (code, variant, name) pro výběr uživatelem.

### b) Uložení výběru uživatele
- Uživatel vybere typ karty (`variant_type`).
- Pomocí `card_id` a `variant` z `cards_variant_types` najdeme správnou variantu (`cm_id`) v `cards_variant`.
- Do `user_collection_items` uložíme `card_id`, `variant` (`cm_id`), a `variant_type`.

## Odpovědi na klíčové otázky
- Jedna karta může mít více typů se stejným variant/cmid: Ano, proto je nutné evidovat i `variant_type`.
- Vždy je jednoznačné, která varianta z `cards_variant` má být použita: Ano, určíme ji pomocí `card_id` a `variant` z `cards_variant_types`.
- Pole `variant_type` se přidává do `user_collection_items`.

## Návrh rozhraní (service/controller)

- **Service metoda:**
  `getTypesForCard(string $cardId): Collection`  
  Vrací seznam typů karet (z `cards_variant_types`) pro danou kartu.

- **Service metoda:**
  `resolveVariantForType(string $cardId, string $variantTypeCode): ?CardsVariant`  
  Najde konkrétní variantu (`cm_id`) pro daný typ a kartu.

- **Controller endpoint:**
  `/collections/{collection}/items/types/{card}`  
  Vrací seznam typů karet pro výběr uživatelem.

---

Tento dokument slouží jako referenční popis klíčové logiky pro evidenci typů a variant karet v uživatelské sbírce. Při implementaci je vhodné se k němu vracet a dále jej rozšiřovat podle vývoje projektu. 