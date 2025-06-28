<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\CollectionItemUpdateRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Factory as ValidatorFactory;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CollectionItemUpdateRequestTest extends TestCase
{
    #[Test]
    public function valid_data_passes_validation()
    {
        $data = [
            'quantity' => 2,
            'condition' => 'excellent',
            'language' => 'english',
            'first_edition' => true,
            'grading' => null,
            'grading_cert' => null,
            'purchase_price' => 15.99,
            'purchase_date' => '2024-01-15',
            'location' => 'B2',
            'note' => 'Updated note',
        ];

        $request = new CollectionItemUpdateRequest();
        $validator = $this->app->make(ValidatorFactory::class)->make($data, $request->rules());
        if (!$validator->passes()) {
            dump($validator->errors()->toArray());
        }
        $this->assertTrue($validator->passes());
    }

    #[Test]
    public function invalid_condition_fails_validation()
    {
        $data = [
            'quantity' => 1,
            'condition' => 'invalid_condition',
            'language' => 'english',
            'first_edition' => false,
        ];

        $request = new CollectionItemUpdateRequest();
        $validator = $this->app->make(ValidatorFactory::class)->make($data, $request->rules());
        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('condition', $validator->errors()->toArray());
    }

    #[Test]
    public function grade_value_required_with_grade_company()
    {
        $data = [
            'quantity' => 1,
            'condition' => 'near_mint',
            'language' => 'english',
            'first_edition' => false,
            'grading' => 'PSA',
            'grading_cert' => null,
        ];

        $request = new CollectionItemUpdateRequest();
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
            'quantity' => 1,
            'condition' => 'invalid_condition',
            'language' => 'english',
            'first_edition' => false,
        ];

        $request = new CollectionItemUpdateRequest();
        $validator = $this->app->make(ValidatorFactory::class)->make($data, $request->rules());
        $validator->passes();
        $errors = $validator->errors()->get('condition');
        $this->assertStringContainsString('Stav karty musí být jedna z hodnot', $errors[0]);
    }
} 