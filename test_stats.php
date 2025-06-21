<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$collection = \App\Models\UserCollection::first();
$service = app(\App\Services\CollectionItemService::class);
$stats = $service->getCollectionStats($collection);

echo "Statistiky kolekce:\n";
echo json_encode($stats, JSON_PRETTY_PRINT);

// Také zkontrolujme jednotlivé položky
echo "\n\nPrvních 5 položek s cenami:\n";
$items = \DB::table('user_collection_items')
    ->select('id', 'purchase_price', 'quantity')
    ->whereNotNull('purchase_price')
    ->where('purchase_price', '>', 0)
    ->limit(5)
    ->get();
    
foreach ($items as $item) {
    echo "ID: {$item->id}, Cena: {$item->purchase_price}, Množství: {$item->quantity}\n";
} 