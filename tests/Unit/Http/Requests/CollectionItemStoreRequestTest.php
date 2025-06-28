<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\CollectionItemStoreRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Factory as ValidatorFactory;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Models\CardsVariant;

class CollectionItemStoreRequestTest extends TestCase
{
    #[Test]
    public function valid_data_passes_validation()
    {
        $data = [
            'card_id' => 'sv6pt5-19',
            'variant_id' => 780912, // Správné pole podle migrace
            'variant_type' => 'normal',
            'quantity' => 1,
            'condition' => 'near_mint',
            'language' => 'english',
            'first_edition' => false,
            'grading' => null,
            'grading_cert' => null,
            'purchase_price' => 10.99,
            'location' => 'A1',
            'note' => 'Test',
        ];
        // CardsVariant::shouldReceive('where')->andReturnSelf();
        // CardsVariant::shouldReceive('first')->andReturn((object)['card_id' => 'sv6pt5-19']);
        $request = new CollectionItemStoreRequest();
        $validator = $this->app->make(ValidatorFactory::class)->make($data, $request->rules());
        if (!$validator->passes()) {
            dump($validator->errors()->toArray());
        }
        $this->assertTrue($validator->passes());
        dump(\DB::table('cards_variant')->where('cm_id', 780912)->where('card_id', 'sv6pt5-19')->first());
    }

    #[Test]
    public function invalid_condition_fails_validation()
    {
        $data = [
            'card_id' => 'sv6pt5-19',
            'variant_id' => 780912,
            'quantity' => 1,
            'condition' => 'invalid',
            'language' => 'english',
            'first_edition' => false,
        ];
        $request = new CollectionItemStoreRequest();
        $validator = $this->app->make(ValidatorFactory::class)->make($data, $request->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('condition', $validator->errors()->toArray());
        dump(\DB::table('cards_variant')->where('cm_id', 780912)->where('card_id', 'sv6pt5-19')->first());
    }

    #[Test]
    public function grading_cert_required_with_grading()
    {
        $data = [
            'card_id' => 'sv6pt5-19',
            'variant_id' => 780912,
            'quantity' => 1,
            'condition' => 'near_mint',
            'language' => 'english',
            'first_edition' => false,
            'grading' => 'PSA',
            'grading_cert' => null, // Toto by mělo způsobit chybu
        ];
        $request = new CollectionItemStoreRequest();
        $validator = $this->app->make(ValidatorFactory::class)->make($data, $request->rules());
        $request->withValidator($validator); // Musím zavolat withValidator pro cross-field validaci
        $this->assertFalse($validator->passes());
        dump($validator->errors()->toArray());
        $this->assertArrayHasKey('grading_cert', $validator->errors()->toArray());
    }

    #[Test]
    public function localization_czech_message()
    {
        Lang::setLocale('cs');
        $data = [
            'card_id' => 'sv6pt5-19',
            'variant_id' => 780912,
            'quantity' => 1,
            'condition' => 'invalid',
            'language' => 'english',
            'first_edition' => false,
        ];
        $request = new CollectionItemStoreRequest();
        $validator = $this->app->make(ValidatorFactory::class)->make($data, $request->rules());
        $validator->passes();
        $errors = $validator->errors()->get('condition');
        $this->assertStringContainsString('Stav karty musí být jedna z hodnot', $errors[0]);
        dump(\DB::table('cards_variant')->where('cm_id', 780912)->where('card_id', 'sv6pt5-19')->first());
    }
} 