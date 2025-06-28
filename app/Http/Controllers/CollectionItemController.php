<?php

namespace App\Http\Controllers;

use App\Models\UserCollection;
use App\Models\UserCollectionItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\CollectionItemStoreRequest;
use App\Http\Requests\CollectionItemUpdateRequest;
use App\Services\CollectionItemService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use App\Rules\ConditionEnum;
use App\Rules\LanguageEnum;

class CollectionItemController extends Controller
{
    protected CollectionItemService $itemService;

    public function __construct(CollectionItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    /**
     * Zobrazí seznam položek v kolekci.
     */
    public function index(UserCollection $collection): Response
    {
        $this->authorize('view', $collection);
        $items = $this->itemService->getItemsForCollection($collection);
        return Inertia::render('Collections/Items/Index', [
            'collection' => $collection->load('user'),
            'items' => $items,
        ]);
    }

    /**
     * Zobrazí formulář pro přidání nové položky do kolekce.
     */
    public function create(UserCollection $collection): Response
    {
        $this->authorize('update', $collection);
        return Inertia::render('Collections/Items/Create', [
            'collection' => $collection->only(['id', 'name']),
        ]);
    }

    /**
     * Uloží novou položku do kolekce.
     */
    public function store(CollectionItemStoreRequest $request, UserCollection $collection)
    {
        $this->authorize('update', $collection);
        
        // Debug logging
        Log::info('CollectionItemController@store: Začínám ukládání', [
            'collection_id' => $collection->id
        ]);
        
        try {
            $validatedData = $request->validated();
            
            Log::info('CollectionItemController@store: Validace prošla', [
                'validated_data' => $validatedData
            ]);

            $dataToStore = [
                'collection_id' => $collection->id,
                'card_id' => $validatedData['card_id'],
                'variant_id' => $validatedData['variant_id'],
                'variant_type' => $validatedData['variant_type'] ?? null,
                'quantity' => $validatedData['quantity'],
                'condition' => $validatedData['condition'],
                'language' => $validatedData['language'],
                'is_first_edition' => $validatedData['first_edition'] ?? false,
                'is_graded' => !empty($validatedData['grading']),
                'grade_company' => $validatedData['grading'] ?? null,
                'grade_value' => $validatedData['grading_cert'] ?? null,
                'purchase_price' => $validatedData['purchase_price'] ?? null,
                'location' => $validatedData['location'] ?? null,
                'notes' => $validatedData['note'] ?? null,
            ];

            Log::info('CollectionItemController@store: Data připravena k uložení', [
                'data_to_store' => $dataToStore
            ]);

            $this->itemService->addItemToCollection($collection, $dataToStore);
            
            Log::info('CollectionItemController@store: Položka úspěšně uložena');
            
            return redirect()->route('collections.show', $collection->id)
                ->with('success', __('collections.items.messages.created'));
                
        } catch (\Exception $e) {
            Log::error('CollectionItemController@store: Chyba při ukládání', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['error' => 'Nastala chyba při ukládání položky: ' . $e->getMessage()]);
        }
    }

    /**
     * Zobrazí formulář pro úpravu položky.
     */
    public function edit(UserCollection $collection, UserCollectionItem $item): Response
    {
        // Only check collection authorization - if user can update collection, they can edit items
        $this->authorize('update', $collection);
        
        // Verify that this item actually belongs to this collection (security check)
        if ($item->collection_id !== $collection->id) {
            abort(422, 'Item does not belong to this collection');
        }

        $item->load(['card', 'variant', 'variantType']);

        $itemDataForForm = [
            'id' => $item->id,
            'card_id' => $item->card_id,
            'card_name' => $item->card->name ?? 'N/A',
            'variant_id' => $item->variant_id,
            'variant_name' => $item->variantType->name ?? 'N/A',
            'image_url' => $item->card->img_file_small ?? $item->card->img_small ?? $item->card->img_file_large ?? $item->card->img_large ?? null,
            
            'condition' => $item->condition,
            'language' => $item->language,
            'quantity' => $item->quantity,
            'purchase_price' => $item->purchase_price,
            'grading' => $item->grade_company,
            'grading_cert' => $item->grade_value,
            'first_edition' => $item->is_first_edition,
            'location' => $item->location,
            'note' => $item->notes,
        ];

        return Inertia::render('Collections/Items/Edit', [
            'collection' => $collection->only(['id', 'name']),
            'collectionId' => $collection->id,
            'itemId' => $item->id,
            'item' => $itemDataForForm,
            'cardData' => $item->card ? $item->card->only([
                'id', 'name', 'img_file_small', 'img_small', 'img_file_large', 'img_large', 
                'set_id', 'number'
            ]) : null,
        ]);
    }

    /**
     * Uloží změny položky.
     */
    public function update(CollectionItemUpdateRequest $request, UserCollection $collection, UserCollectionItem $item)
    {
        // Only check collection authorization - if user can update collection, they can update items
        $this->authorize('update', $collection);
        
        // Verify that this item actually belongs to this collection (security check)
        if ($item->collection_id !== $collection->id) {
            abort(422, 'Item does not belong to this collection');
        }
        
        $validatedData = $request->validated();

        $dataToUpdate = [
            'quantity' => $validatedData['quantity'],
            'condition' => $validatedData['condition'],
            'language' => $validatedData['language'],
            'is_first_edition' => $validatedData['first_edition'] ?? false,
            'is_graded' => !empty($validatedData['grading']),
            'grade_company' => $validatedData['grading'] ?? null,
            'grade_value' => $validatedData['grading_cert'] ?? null,
            'purchase_price' => $validatedData['purchase_price'] ?? null,
            'location' => $validatedData['location'] ?? null,
            'notes' => $validatedData['note'] ?? null,
        ];

        $this->itemService->updateItemInCollection($collection, $item, $dataToUpdate);
        return redirect()->route('collections.show', $collection->id)
            ->with('success', __('collections.items.messages.updated'));
    }

    /**
     * Smaže položku ze sbírky.
     */
    public function destroy(UserCollection $collection, UserCollectionItem $item)
    {
        // Only check collection authorization - if user can update collection, they can delete items
        $this->authorize('update', $collection);

        // Verify that this item actually belongs to this collection (security check)
        if ($item->collection_id !== $collection->id) {
            abort(422, 'Item does not belong to this collection');
        }

        $this->itemService->deleteItemFromCollection($collection, $item);
        return redirect()->route('collections.show', $collection->id)
            ->with('success', __('collections.items.messages.deleted'));
    }

    /**
     * Hromadné mazání položek.
     */
    public function bulkDelete(Request $request, UserCollection $collection)
    {
        $this->authorize('update', $collection);
        
        $request->validate([
            'item_ids' => 'required|array|min:1|max:100',
            'item_ids.*' => 'integer|exists:user_collection_items,id'
        ]);

        $itemIds = $request->input('item_ids');
        
        // Ověř, že všechny položky patří do této kolekce
        $items = UserCollectionItem::whereIn('id', $itemIds)
            ->where('collection_id', $collection->id)
            ->get();

        if ($items->count() !== count($itemIds)) {
            return redirect()->route('collections.show', $collection->id)
                ->withErrors(['error' => 'Některé položky nepatří do této kolekce nebo neexistují']);
        }

        foreach ($items as $item) {
            $this->itemService->deleteItemFromCollection($collection, $item);
        }

        return redirect()->route('collections.show', $collection->id)
            ->with('success', __('collections.items.messages.bulk_deleted', ['count' => $items->count()]));
    }

    /**
     * Hromadná duplikace položek.
     */
    public function bulkDuplicate(Request $request, UserCollection $collection)
    {
        $this->authorize('update', $collection);
        
        $request->validate([
            'item_ids' => 'required|array|min:1|max:50',
            'item_ids.*' => 'integer|exists:user_collection_items,id'
        ]);

        $itemIds = $request->input('item_ids');
        
        // Ověř, že všechny položky patří do této kolekce
        $items = UserCollectionItem::whereIn('id', $itemIds)
            ->where('collection_id', $collection->id)
            ->get();

        if ($items->count() !== count($itemIds)) {
            return redirect()->route('collections.show', $collection->id)
                ->withErrors(['error' => 'Některé položky nepatří do této kolekce nebo neexistují']);
        }

        $duplicatedCount = 0;
        foreach ($items as $item) {
            // Zkusíme najít existující položku se všemi stejnými parametry (podle unique constraintu)
            $existingItem = UserCollectionItem::where('collection_id', $collection->id)
                ->where('card_id', $item->card_id)
                ->where('variant_id', $item->variant_id)
                ->where('variant_type', $item->variant_type)
                ->where('condition', $item->condition)
                ->where('language', $item->language)
                ->where('purchase_price', $item->purchase_price)
                ->first();

            if ($existingItem) {
                // Pokud existuje úplně identická položka, zvýšíme quantity
                $existingItem->quantity += $item->quantity;
                $existingItem->save();
                $duplicatedCount++;
            } else {
                // Pokud neexistuje, vytvoříme novou položku (duplikát)
                $dataToStore = [
                    'card_id' => $item->card_id,
                    'variant_id' => $item->variant_id,
                    'variant_type' => $item->variant_type,
                    'quantity' => $item->quantity,
                    'condition' => $item->condition,
                    'language' => $item->language,
                    'is_first_edition' => $item->is_first_edition,
                    'is_graded' => $item->is_graded,
                    'grade_company' => $item->grade_company,
                    'grade_value' => $item->grade_value,
                    'purchase_price' => $item->purchase_price,
                    'location' => $item->location,
                    'notes' => $item->notes,
                ];
                
                $this->itemService->addItemToCollection($collection, $dataToStore);
                $duplicatedCount++;
            }
        }

        return redirect()->route('collections.show', $collection->id)
            ->with('success', __('collections.items.messages.bulk_duplicated', ['count' => $duplicatedCount]));
    }

    /**
     * Hromadná úprava položek.
     */
    public function bulkEdit(Request $request, UserCollection $collection)
    {
        $this->authorize('update', $collection);
        
        $request->validate([
            'item_ids' => 'required|array|min:1|max:100',
            'item_ids.*' => 'integer|exists:user_collection_items,id',
            'updates' => 'required|array|min:1',
            'updates.condition' => ['nullable', 'string', new ConditionEnum()],
            'updates.language' => ['nullable', 'string', new LanguageEnum()],
            'updates.location' => 'nullable|string|max:100',
            'updates.purchase_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99', 'regex:/^\d{1,8}(\.\d{1,2})?$/'],
            'updates.quantity' => 'nullable|integer|min:1|max:999',
            'updates.first_edition' => 'nullable|boolean',
            'updates.grading' => 'nullable|string|max:50',
            'updates.grading_cert' => 'nullable|string|max:10',
            'updates.note' => 'nullable|string|max:65535',
        ]);

        $itemIds = $request->input('item_ids');
        $updates = $request->input('updates');
        
        // Ověř, že všechny položky patří do této kolekce
        $items = UserCollectionItem::whereIn('id', $itemIds)
            ->where('collection_id', $collection->id)
            ->get();

        if ($items->count() !== count($itemIds)) {
            return response()->json(['error' => 'Některé položky nepatří do této kolekce'], 422);
        }

        // Cross-field validace pro grading
        if ((!empty($updates['grading']) && empty($updates['grading_cert'])) || 
            (!empty($updates['grading_cert']) && empty($updates['grading']))) {
            return response()->json([
                'error' => 'Při hromadné úpravě gradingu musí být vyplněny oba údaje - společnost i certifikát'
            ], 422);
        }

        // Ověř, že je specifikována alespoň jedna změna
        $filteredUpdates = array_filter($updates, function($value) {
            return $value !== null && $value !== '';
        });
        
        if (empty($filteredUpdates)) {
            return response()->json(['error' => 'Musí být specifikována alespoň jedna změna'], 422);
        }

        $updatedCount = 0;
        foreach ($items as $item) {
            // Mapování názvů polí z formuláře do databáze (stejné jako v store/update)
            $dataToUpdate = [];
            foreach ($filteredUpdates as $field => $value) {
                switch ($field) {
                    case 'first_edition':
                        $dataToUpdate['is_first_edition'] = (bool) $value;
                        break;
                    case 'grading':
                        $dataToUpdate['is_graded'] = !empty($value);
                        $dataToUpdate['grade_company'] = $value;
                        break;
                    case 'grading_cert':
                        $dataToUpdate['grade_value'] = $value;
                        break;
                    case 'note':
                        $dataToUpdate['notes'] = $value;
                        break;
                    default:
                        $dataToUpdate[$field] = $value;
                        break;
                }
            }
            
            if (!empty($dataToUpdate)) {
                $this->itemService->updateItemInCollection($collection, $item, $dataToUpdate);
                $updatedCount++;
            }
        }

        return response()->json([
            'message' => __('collections.items.messages.bulk_updated', ['count' => $updatedCount])
        ]);
    }

    /**
     * Export položek kolekce.
     */
    public function export(Request $request, UserCollection $collection)
    {
        $this->authorize('view', $collection);
        
        $request->validate([
            'item_ids' => 'nullable|array',
            'item_ids.*' => 'integer|exists:user_collection_items,id',
            'format' => 'nullable|string|in:csv,json'
        ]);

        $format = $request->input('format', 'csv');
        $itemIds = $request->input('item_ids');
        
        // Pokud nejsou specifikované ID, exportuj všechny položky
        $query = UserCollectionItem::where('collection_id', $collection->id)
            ->with(['card', 'variant', 'variantType']);
            
        if ($itemIds) {
            $query->whereIn('id', $itemIds);
        }
        
        $items = $query->get();

        if ($format === 'json') {
            return response()->json($items);
        }

        // CSV export
        $filename = "collection_{$collection->id}_export_" . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($items) {
            $file = fopen('php://output', 'w');
            
            // CSV header
            fputcsv($file, [
                'ID', 'Card Name', 'Set', 'Number', 'Condition', 'Language', 
                'Quantity', 'First Edition', 'Graded', 'Grade Company', 'Grade Value',
                'Purchase Price', 'Location', 'Notes'
            ]);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->card->name ?? '',
                    $item->card->set_name ?? '',
                    $item->card->number ?? '',
                    $item->condition,
                    $item->language,
                    $item->quantity,
                    $item->is_first_edition ? 'Yes' : 'No',
                    $item->is_graded ? 'Yes' : 'No',
                    $item->grade_company ?? '',
                    $item->grade_value ?? '',
                    $item->purchase_price ?? '',
                    $item->location ?? '',
                    $item->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 