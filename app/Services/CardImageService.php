<?php

namespace App\Services;

use App\Models\Card;
use App\Models\UserCollectionItem;
use Illuminate\Support\Facades\Cache;

class CardImageService
{
    /**
     * Get image data for a single card
     */
    public function getImageData($cardId, $size = 'small')
    {
        return Cache::rememberForever("card_image_{$cardId}_{$size}", function() use ($cardId, $size) {
            $card = Card::select('id', 'set_id', 'number', 'img_small', 'img_file_small', 'img_large', 'img_file_large')
                       ->find($cardId);
            
            if (!$card) {
                return $this->getPlaceholderData();
            }
            
            return $this->buildImageData($card, $size);
        });
    }
    
    /**
     * Get image data for multiple cards (bulk loading)
     */
    public function getBulkImageData(array $cardIds, $size = 'small')
    {
        if (empty($cardIds)) {
            return [];
        }
        
        $cacheKey = "bulk_images_" . md5(implode(',', $cardIds) . '_' . $size);
        
        return Cache::rememberForever($cacheKey, function() use ($cardIds, $size) {
            $cards = Card::whereIn('id', $cardIds)
                        ->select('id', 'set_id', 'number', 'img_small', 'img_file_small', 'img_large', 'img_file_large')
                        ->get()
                        ->keyBy('id');
            
            $result = [];
            foreach ($cardIds as $cardId) {
                $card = $cards->get($cardId);
                if ($card) {
                    $result[$cardId] = $this->buildImageData($card, $size);
                } else {
                    $result[$cardId] = $this->getPlaceholderData();
                }
            }
            
            return $result;
        });
    }
    
    /**
     * Get image data for cards by set (useful for preloading)
     */
    public function getSetImageData($setId, $size = 'small')
    {
        $cacheKey = "set_images_{$setId}_{$size}";
        
        return Cache::rememberForever($cacheKey, function() use ($setId, $size) {
            $cards = Card::where('set_id', $setId)
                        ->select('id', 'set_id', 'number', 'img_small', 'img_file_small', 'img_large', 'img_file_large')
                        ->get();
            
            $result = [];
            foreach ($cards as $card) {
                $result[$card->id] = $this->buildImageData($card, $size);
            }
            
            return $result;
        });
    }
    
    /**
     * Build image data structure for a card
     */
    private function buildImageData($card, $size)
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
            'size' => $size,
            'cached_at' => now()->toISOString()
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
            'size' => 'small',
            'cached_at' => now()->toISOString()
        ];
    }
    
    /**
     * Preload images for a collection's cards
     */
    public function preloadCollectionImages($collectionId, $size = 'small')
    {
        $cardIds = UserCollectionItem::where('user_collection_id', $collectionId)
                                            ->pluck('card_id')
                                            ->unique()
                                            ->toArray();
        
        return $this->getBulkImageData($cardIds, $size);
    }
    
    /**
     * Clear cache for specific card (if needed for maintenance)
     */
    public function clearCardCache($cardId)
    {
        Cache::forget("card_image_{$cardId}_small");
        Cache::forget("card_image_{$cardId}_large");
    }
} 