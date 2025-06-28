<?php

namespace Tests\Feature\Frontend;

use Tests\TestCase;
use App\Http\Requests\CollectionItemStoreRequest;
use App\Http\Requests\CollectionItemUpdateRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;

class CollectionItemValidationTest extends TestCase
{

    #[Test]
    public function it_validates_required_fields()
    {
        $request = new CollectionItemStoreRequest();
        $validator = Validator::make([
            'card_id' => '',
            'condition' => '',
            'language' => '',
            'quantity' => '',
        ], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('card_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('condition', $validator->errors()->toArray());
        $this->assertArrayHasKey('language', $validator->errors()->toArray());
        $this->assertArrayHasKey('quantity', $validator->errors()->toArray());
    }

    #[Test]
    public function it_validates_condition_enum()
    {
        $request = new CollectionItemStoreRequest();
        $validator = Validator::make([
            'card_id' => 'sv6pt5-19',
            'condition' => 'invalid_condition',
            'language' => 'english',
            'quantity' => 1,
        ], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('condition', $validator->errors()->toArray());
    }

    #[Test]
    public function it_validates_language_enum()
    {
        $request = new CollectionItemStoreRequest();
        $validator = Validator::make([
            'card_id' => 'sv6pt5-19',
            'condition' => 'near_mint',
            'language' => 'invalid_language',
            'quantity' => 1,
        ], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('language', $validator->errors()->toArray());
    }

    #[Test]
    public function it_validates_quantity_range()
    {
        $request = new CollectionItemStoreRequest();
        
        // Quantity příliš nízké
        $validator = Validator::make([
            'card_id' => 'sv6pt5-19',
            'condition' => 'near_mint',
            'language' => 'english',
            'quantity' => 0,
        ], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('quantity', $validator->errors()->toArray());

        // Quantity příliš vysoké
        $validator = Validator::make([
            'card_id' => 'sv6pt5-19',
            'condition' => 'near_mint',
            'language' => 'english',
            'quantity' => 1000,
        ], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('quantity', $validator->errors()->toArray());
    }

    #[Test]
    public function it_validates_purchase_price()
    {
        $request = new CollectionItemStoreRequest();
        $validator = Validator::make([
            'card_id' => 'sv6pt5-19',
            'condition' => 'near_mint',
            'language' => 'english',
            'quantity' => 1,
            'purchase_price' => -10, // Záporná cena
        ], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('purchase_price', $validator->errors()->toArray());
    }

    #[Test]
    public function it_validates_string_length_limits()
    {
        $request = new CollectionItemStoreRequest();
        $validator = Validator::make([
            'card_id' => 'sv6pt5-19',
            'condition' => 'near_mint',
            'language' => 'english',
            'quantity' => 1,
            'location' => str_repeat('a', 101), // Příliš dlouhé (max 100)
            'note' => str_repeat('b', 65536), // Příliš dlouhé (max 65535)
        ], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('location', $validator->errors()->toArray());
        $this->assertArrayHasKey('note', $validator->errors()->toArray());
    }

    #[Test]
    public function it_accepts_valid_data()
    {
        $request = new CollectionItemStoreRequest();
        $validator = Validator::make([
            'card_id' => 'sv6pt5-19',
            'variant_id' => 780912,
            'condition' => 'near_mint',
            'language' => 'english',
            'quantity' => 1,
            'purchase_price' => 10.50,
            'location' => 'Box 1',
            'note' => 'Test note',
        ], $request->rules());

        $this->assertFalse($validator->fails());
        $this->assertEmpty($validator->errors()->toArray());
    }

    #[Test]
    public function it_validates_update_request()
    {
        $request = new CollectionItemUpdateRequest();
        $validator = Validator::make([
            'condition' => 'invalid_condition',
            'language' => 'invalid_language',
            'quantity' => 0,
        ], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('condition', $validator->errors()->toArray());
        $this->assertArrayHasKey('language', $validator->errors()->toArray());
        $this->assertArrayHasKey('quantity', $validator->errors()->toArray());
    }

    #[Test]
    public function it_validates_grading_cross_field_rules()
    {
        $request = new CollectionItemStoreRequest();
        
        // Simulace cross-field validace pomocí withValidator
        $data = [
            'card_id' => 'sv6pt5-19',
            'variant_id' => 780912,
            'condition' => 'near_mint',
            'language' => 'english',
            'quantity' => 1,
            'grading' => 'psa',
            'grading_cert' => '', // Prázdné
        ];
        
        $validator = Validator::make($data, $request->rules());
        
        // Aplikace withValidator logiky
        $request->withValidator($validator);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('grading_cert', $validator->errors()->toArray());
    }

    #[Test]
    public function it_validates_grading_cross_field_rules_reverse()
    {
        $request = new CollectionItemStoreRequest();
        
        $data = [
            'card_id' => 'sv6pt5-19',
            'variant_id' => 780912,
            'condition' => 'near_mint',
            'language' => 'english',
            'quantity' => 1,
            'grading' => '', // Prázdné
            'grading_cert' => '9.5',
        ];
        
        $validator = Validator::make($data, $request->rules());
        $request->withValidator($validator);
        
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('grading', $validator->errors()->toArray());
    }
} 