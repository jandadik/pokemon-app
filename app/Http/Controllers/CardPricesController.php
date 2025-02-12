<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\PricesTcg;
use App\Models\PricesCardmarket;
use Illuminate\Http\JsonResponse;

class CardPricesController extends Controller
{
    public function show(Card $card): JsonResponse
    {
        $latestTcgPrice = PricesTcg::where('card_id', $card->id)
            ->latest('updated_at')
            ->first();

        $latestCardmarketPrice = PricesCardmarket::where('card_id', $card->id)
            ->latest('updated_at')
            ->first();

        $prices = [
            'tcgplayer' => [
                'prices' => json_decode($latestTcgPrice?->prices ?? '{}'),
                'url' => $card->url_tcgplayer,
                'updatedAt' => $latestTcgPrice?->updated_at
            ],
            'cardmarket' => [
                'prices' => json_decode($latestCardmarketPrice?->prices ?? '{}'),
                'url' => $card->url_cardmarket,
                'updatedAt' => $latestCardmarketPrice?->updated_at
            ]
        ];

        return response()->json([
            'data' => $prices
        ]);
    }
} 