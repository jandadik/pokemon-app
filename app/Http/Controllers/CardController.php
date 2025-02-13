<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Set;
use App\Models\Card;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class CardController extends Controller
{
    /**
     * Zobrazí seznam karet pro konkrétní set
     */
    public function index(Set $set)
    {
        $cards = Cache::remember('set_cards_' . $set->id, 3600, function () use ($set) {
            return Card::where('set_id', $set->id)
                ->select(['id', 'name', 'number', 'img_file_small', 'rarity', 'set_id'])
                ->orderByRaw('CAST(number AS UNSIGNED) ASC')
                ->get()
                ->map(function ($card) {
                    $card->local_image = $card->img_file_small 
                        ? '/images/' . str_replace(['\\', '.png'], ['/', '.jpg'], $card->img_file_small)
                        : '/images/placeholder.jpg';
                    
                    return $card;
                });
        });

        return Inertia::render('Cards/Index', [
            'set' => $set,
            'cards' => $cards
        ]);
    }

    /**
     * Zobrazí detail konkrétní karty
     */
    public function show(Set $set, Card $card)
    {
        return Inertia::render('Cards/Show', [
            'set' => $set,
            'card' => $card
        ]);
    }
}
