<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Set;
use App\Models\Card;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CardController extends Controller
{
    /**
     * Zobrazí seznam karet pro konkrétní set
     */
    public function index(Set $set)
    {
        $cards = $set->cards()
            ->orderBy('id', 'asc')
            ->orderBy('name', 'asc')  // záložní řazení podle jména
            ->get();

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
