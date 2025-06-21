<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CardControllerVariantsTest extends TestCase
{
    #[Test]
    public function it_returns_variants_for_card()
    {
        // Použití existující karty z databáze
        $response = $this->getJson(route('catalog.cards.variants', ['card' => 'sv3pt5-4']));

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'code',
                    'variant',
                    'name',
                    'description'
                ]
            ]);

        // Ověření, že máme nějaké varianty
        $variants = $response->json();
        $this->assertNotEmpty($variants);
        
        // Ověření struktury první varianty
        if (count($variants) > 0) {
            $this->assertArrayHasKey('code', $variants[0]);
            $this->assertArrayHasKey('name', $variants[0]);
        }
    }

    #[Test]
    public function it_returns_empty_array_for_card_without_variants()
    {
        // Použití existující karty, která nemá varianty
        $response = $this->getJson(route('catalog.cards.variants', ['card' => 'cel25-1']));

        $response->assertStatus(200)
            ->assertJson([]);
    }

    #[Test]
    public function it_returns_empty_array_for_nonexistent_card()
    {
        // Použití neexistující karty
        $response = $this->getJson(route('catalog.cards.variants', ['card' => 'nonexistent-card-id']));

        $response->assertStatus(200)
            ->assertJson([]);
    }

    #[Test]
    public function it_returns_variant_details_with_prices()
    {
        // Použití existující karty a variant type code
        $response = $this->getJson(route('catalog.cards.variants.details', [
            'card' => 'sv3pt5-4',
            'variantTypeCode' => 1
        ]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'cm_id',
                'card_id',
                'card_name',
                'set_id',
                'number',
                'rarity',
                'supertype',
                'types',
                'image_url',
                'variant_id',
                'variant_type_code',
                'variant_type_name',
                'collector_number',
                'ptcgo_code',
                'tcgplayer_id',
                'prices' => [
                    'cardmarket' => [
                        'low',
                        'trend',
                        'avg1',
                        'avg7',
                        'avg30'
                    ],
                    'tcgplayer' => [
                        'low',
                        'trend'
                    ]
                ]
            ]);

        $data = $response->json();
        $this->assertEquals('sv3pt5-4', $data['card_id']);
        $this->assertEquals(1, $data['variant_type_code']);
        $this->assertEquals('Normal', $data['variant_type_name']);
        $this->assertIsNumeric($data['cm_id']);
        $this->assertIsNumeric($data['variant_id']);
        $this->assertEquals($data['cm_id'], $data['variant_id']); // Měly by být stejné
    }

    #[Test]
    public function it_returns_404_for_variant_details_without_variants()
    {
        // Použití existující karty bez variant
        $response = $this->getJson(route('catalog.cards.variants.details', [
            'card' => 'cel25-1',
            'variantTypeCode' => 1
        ]));

        $response->assertStatus(404)
            ->assertJson(['error' => 'Variant not found']);
    }

    #[Test]
    public function it_returns_404_for_nonexistent_variant_type()
    {
        $response = $this->getJson(route('catalog.cards.variants.details', [
            'card' => 'cel25-1',
            'variantTypeCode' => 999
        ]));

        $response->assertStatus(404)
            ->assertJson(['error' => 'Variant not found']);
    }

    #[Test]
    public function it_returns_404_for_nonexistent_card_variant_details()
    {
        $response = $this->getJson(route('catalog.cards.variants.details', [
            'card' => 'nonexistent-card',
            'variantTypeCode' => 1
        ]));

        $response->assertStatus(404)
            ->assertJson(['error' => 'Variant not found']);
    }

    #[Test]
    public function it_validates_route_parameters()
    {
        // Test s neplatným variant type code (ne-numerický)
        $response = $this->getJson('/cards/cel25-1/variants/invalid-code');
        
        // Laravel by měl vrátit 404 pro neplatný route parametr
        $response->assertStatus(404);
    }

    #[Test]
    public function it_handles_variants_endpoint_structure()
    {
        // Test struktury odpovědi pro variants endpoint
        $response = $this->getJson(route('catalog.cards.variants', ['card' => 'cel25-1']));

        $response->assertStatus(200)
            ->assertHeader('content-type', 'application/json');

        // Odpověď by měla být pole (i když prázdné)
        $this->assertIsArray($response->json());
    }

    #[Test]
    public function it_handles_variant_details_endpoint_structure()
    {
        // Test struktury chybové odpovědi pro variant details endpoint
        $response = $this->getJson(route('catalog.cards.variants.details', [
            'card' => 'cel25-1',
            'variantTypeCode' => 1
        ]));

        $response->assertStatus(404)
            ->assertHeader('content-type', 'application/json')
            ->assertJsonStructure(['error']);
    }
} 