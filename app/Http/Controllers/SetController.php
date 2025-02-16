<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Set;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

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

    /**
     * Display a listing of the resource.
     */
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


        return inertia(
            'Set/Index',
            [
                'sets' => $sets
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Set $set)
    {
        Gate::authorize(ability: 'admin.access');
        return inertia(
            'Set/Show',
            [
                'set' => $set
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
