<?php

namespace Tests\Unit\Services;

use App\Models\UserCollection;
use App\Services\CollectionItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class CollectionItemServiceTest extends \Tests\TestCase
{
    protected CollectionItemService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CollectionItemService();
    }

    #[Test]
    public function it_returns_paginated_items_with_filters_and_sorting()
    {
        $collection = UserCollection::has('items')->first();
        $this->assertNotNull($collection, 'V databázi musí být alespoň jedna kolekce s položkami.');

        $result = $this->service->getItemsForCollectionPaginated($collection, [], 'created_at', 'desc', 10);
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
        $this->assertIsIterable($result->items());
    }

    #[Test]
    public function it_returns_collection_stats()
    {
        $collection = UserCollection::has('items')->first();
        $this->assertNotNull($collection, 'V databázi musí být alespoň jedna kolekce s položkami.');

        $stats = $this->service->getCollectionStats($collection);
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_cards', $stats);
        $this->assertArrayHasKey('unique_cards', $stats);
        $this->assertArrayHasKey('total_value', $stats);
    }

    #[Test]
    public function it_returns_filter_options()
    {
        $collection = UserCollection::has('items')->first();
        $this->assertNotNull($collection, 'V databázi musí být alespoň jedna kolekce s položkami.');

        $options = $this->service->getFilterOptions($collection);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('rarities', $options);
        $this->assertArrayHasKey('languages', $options);
        $this->assertArrayHasKey('conditions', $options);
    }
} 