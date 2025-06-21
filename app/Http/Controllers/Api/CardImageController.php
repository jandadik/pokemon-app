<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CardImageController extends Controller
{
    /**
     * Get image URL for a single card
     */
    public function getImageUrl(Request $request, $cardId)
    {
        $size = $request->get('size', 'small');
        
        $imageData = Cache::rememberForever("card_image_{$cardId}_{$size}", function() use ($cardId, $size) {
            $card = Card::select('id', 'set_id', 'number', 'img_small', 'img_file_small', 'img_large', 'img_file_large')
                       ->find($cardId);
            
            if (!$card) {
                return $this->getPlaceholderData();
            }
            
            return $this->getImageData($card, $size);
        });
        
        return response()->json($imageData)
            ->header('Cache-Control', 'public, max-age=31536000') // 1 year
            ->header('ETag', md5($cardId . $size));
    }
    
    /**
     * Get image URLs for multiple cards (bulk loading)
     */
    public function getBulkImageUrls(Request $request)
    {
        $cardIds = explode(',', $request->get('ids', ''));
        $size = $request->get('size', 'small');
        
        if (empty($cardIds) || empty($cardIds[0])) {
            return response()->json([]);
        }
        
        $cacheKey = "bulk_images_" . md5(implode(',', $cardIds) . '_' . $size);
        
        $result = Cache::rememberForever($cacheKey, function() use ($cardIds, $size) {
            $cards = Card::whereIn('id', $cardIds)
                        ->select('id', 'set_id', 'number', 'img_small', 'img_file_small', 'img_large', 'img_file_large')
                        ->get()
                        ->keyBy('id');
            
            $result = [];
            foreach ($cardIds as $cardId) {
                $card = $cards->get($cardId);
                if ($card) {
                    $result[$cardId] = $this->getImageData($card, $size);
                } else {
                    $result[$cardId] = $this->getPlaceholderData();
                }
            }
            
            return $result;
        });
        
        return response()->json($result)
            ->header('Cache-Control', 'public, max-age=31536000')
            ->header('ETag', md5(implode(',', $cardIds) . $size));
    }
    
    /**
     * Get image data for a card based on size
     */
    private function getImageData($card, $size)
    {
        $fileField = "img_file_{$size}";
        $urlField = "img_{$size}";
        
        $url = null;
        $fallback = null;
        
        // Priority 1: Local file
        if ($card->$fileField) {
            $url = '/images/' . str_replace('\\', '/', $card->$fileField);
        }
        
        // Priority 2: External URL
        if ($card->$urlField) {
            $fallback = $card->$urlField;
        }
        
        // Priority 3: Constructed path
        if (!$url && !$fallback && $card->set_id && $card->number) {
            $suffix = $size === 'large' ? '_hires' : '';
            $url = "/images/card_images/{$card->set_id}/{$card->number}{$suffix}.png";
        }
        
        return [
            'url' => $url ?: '/images/placeholder.jpg',
            'fallback' => $fallback ?: '/images/placeholder.jpg',
            'cardId' => $card->id,
            'size' => $size
        ];
    }
    
    /**
     * Get placeholder data
     */
    private function getPlaceholderData()
    {
        return [
            'url' => '/images/placeholder.jpg',
            'fallback' => '/images/placeholder.jpg',
            'cardId' => null,
            'size' => 'small'
        ];
    }
} 