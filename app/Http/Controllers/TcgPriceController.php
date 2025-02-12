<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\PricesTcg;
use Illuminate\Http\JsonResponse;

class TcgPriceController extends Controller
{
    public function show(Card $card): JsonResponse
    {
        $latestPrice = PricesTcg::where('card_id', $card->id)
            ->latest('updated_at')
            ->first();

        return response()->json([
            'data' => [
                'prices' => [
                    'price_low' => $latestPrice?->price_low,
                    'price_mid' => $latestPrice?->price_mid,
                    'price_high' => $latestPrice?->price_high,
                    'price_market' => $latestPrice?->price_market,
                    'price_direct_low' => $latestPrice?->price_direct_low,
                ],
                'url' => $card->url_tcgplayer,
                'updatedAt' => $latestPrice?->updated_at
            ]
        ]);
    }
} 