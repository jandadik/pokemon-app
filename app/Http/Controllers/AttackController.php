<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\JsonResponse;

class AttackController extends Controller
{
    public function index(Card $card): JsonResponse
    {
        $attacks = $card->attacks()
            ->select(['name', 'cost', 'damage', 'text', 'converted_energy_cost'])
            ->get();

        return response()->json([
            'data' => $attacks
        ]);
    }
} 