<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Set;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;

class SetController extends Controller
{
    private $seriesOrder = [
        'Scarlet & Violet',
        'Sword & Shield',
        'Sun & Moon',
        'XY',
        'Black & White',
        'HeartGold & SoulSilver',
        'Platinum',
        'Diamond & Pearl',
        'EX',
        'e-Card',
        'Neo',
        'Gym',
        'Base',
        'Other'
    ];

    public function index()
    {
        $sets = Cache::remember('sets_grouped', 3600, function () {
            return Set::orderBy('release_date', 'desc')
                ->get()
                ->groupBy('series')
                ->sortBy(function ($series, $key) {
                    return array_search($key, $this->seriesOrder) ?? PHP_INT_MAX;
                });
        });

        return Inertia::render('Sets/Index', [
            'sets' => $sets
        ]);
    }

    public function cards(Set $set)
    {
        // Prozatím jen přesměrujeme zpět, implementaci dodáme později
        return redirect()->route('sets.index');
    }
}
