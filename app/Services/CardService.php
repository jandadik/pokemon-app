<?php

namespace App\Services;

use App\Models\Card;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection; // Alias pro Eloquent kolekci
use Illuminate\Support\Facades\DB; // Přidáno pro DB::raw, pokud by bylo potřeba

class CardService
{
    /**
     * Připraví vyhledávací řetězec pro fulltextové vyhledávání v boolean módu.
     * Přidá '+' před každý termín (pro AND logiku) a '*' na konec (pro wildcard).
     * Odstraní některé speciální znaky, které by mohly narušit dotaz.
     *
     * @param string $queryString Vstupní vyhledávací řetězec.
     * @return string Připravený řetězec pro boolean mode fulltext.
     */
    public function prepareBooleanSearchQuery(string $queryString): string
    {
        // 1. Odstranit všechny znaky, které nejsou písmena, čísla, mezery, pomlčky, podtržítka.
        $cleanedQuery = preg_replace('/[^\p{L}\p{N}\s\-_]/', '', $queryString);

        // 2. Rozdělit na termíny podle mezer.
        $searchTerms = preg_split('/\s+/', trim($cleanedQuery), -1, PREG_SPLIT_NO_EMPTY);
        
        if (empty($searchTerms)) {
            return '';
        }

        $booleanTerms = [];
        foreach ($searchTerms as $term) {
            if (preg_match('/^[\-_]+$/', $term)) {
                continue; 
            }
            if (empty($term)) {
                continue;
            }

            // Pokud je termín čistě číselný a jeho délka je menší než 3 (tj. 1 nebo 2 znaky),
            // vynecháme ho z fulltextového boolean dotazu, protože by pravděpodobně nebyl 
            // efektivně zpracován kvůli innodb_ft_min_token_size (typicky 3).
            if (ctype_digit($term) && mb_strlen($term) < 3) {
                continue; // Vynechat krátké číselné termy
            }

            $booleanTerms[] = '+' . $term . '*';
        }

        // Pokud po odstranění krátkých číselných termů nezůstal žádný validní termín,
        // vrátíme prázdný string, aby se neprováděl prázdný MATCH AGAINST dotaz.
        if (empty($booleanTerms) && !empty($searchTerms)) { // !empty($searchTerms) pro případ, že vstup byl např. jen "1"
            return '';
        }

        return implode(' ', $booleanTerms);
    }

    /**
     * Vyhledá karty pro rychlý lookup na základě zadaného řetězce pomocí fulltextu.
     * Prohledává název, PTCGO kód a textové číslo karty.
     * Vrací pouze základní informace včetně obrázku.
     *
     * @param string $queryString Vyhledávací řetězec.
     * @param int $limit Maximální počet vrácených výsledků.
     * @return Collection Kolekce asociativních polí s daty karet.
     */
    public function lookupCards(string $queryString, int $limit = 15): Collection
    {
        if (empty($queryString) || mb_strlen($queryString) < 2) {
            return collect();
        }

        $booleanSearchQuery = $this->prepareBooleanSearchQuery($queryString);

        if (empty($booleanSearchQuery)) {
            return collect();
        }

        /** @var EloquentCollection<Card> $cards */
        $cards = Card::query()
            ->whereRaw('MATCH(name, number_txt, ptcgo_code) AGAINST(? IN BOOLEAN MODE)', [$booleanSearchQuery])
            ->select(['id', 'name', 'set_id', 'number', 'number_txt', 'img_file_small', 'ptcgo_code'])
            ->with('set:id,name')
            ->limit($limit)
            ->get();

        return $cards->map(function (Card $card) {
            // Zajistíme, že cesta k obrázku používá správná lomítka (forward slashes)
            $imagePath = str_replace('\\', '/', $card->img_file_small);
            return [
                'card_id'       => $card->id,
                'image_url'     => url('img/cards/small/' . $imagePath),
                'img_file_small' => $card->img_file_small,
                'name'          => $card->name,
                'set_name'      => $card->set ? $card->set->name : null,
                'number'        => $card->number_txt,
                'ptcgo_code'    => $card->ptcgo_code,
            ];
        });
    }
} 