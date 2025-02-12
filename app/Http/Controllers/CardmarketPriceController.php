<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\PricesCm;
use Illuminate\Http\JsonResponse;

class CardmarketPriceController extends Controller
{
    public function show(Card $card): JsonResponse
    {
        $latestPrice = PricesCm::where('card_id', $card->id)
            ->latest('updated_at')
            ->first();

        return response()->json([
            'data' => [
                'prices' => [
                    'average_sell_price' => $latestPrice?->average_sell_price,
                    'low_price' => $latestPrice?->low_price,
                    'trend_price' => $latestPrice?->trend_price,
                    'german_pro_low' => $latestPrice?->german_pro_low,
                    'suggested_price' => $latestPrice?->suggested_price,
                    'reverse_holo_sell' => $latestPrice?->reverse_holo_sell,
                    'reverse_holo_low' => $latestPrice?->reverse_holo_low,
                    'reverse_holo_trend' => $latestPrice?->reverse_holo_trend,
                    'low_price_ex_plus' => $latestPrice?->low_price_ex_plus,
                    'avg1' => $latestPrice?->avg1,
                    'avg7' => $latestPrice?->avg7,
                    'avg30' => $latestPrice?->avg30,
                    'reverse_holo_avg1' => $latestPrice?->reverse_holo_avg1,
                    'reverse_holo_avg7' => $latestPrice?->reverse_holo_avg7,
                    'reverse_holo_avg30' => $latestPrice?->reverse_holo_avg30
                ],
                'url' => $card->url_cardmarket,
                'updatedAt' => $latestPrice?->updated_at
            ]
        ]);
    }
} 