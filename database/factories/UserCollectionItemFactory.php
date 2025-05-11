<?php

namespace Database\Factories;

use App\Models\UserCollection;
use App\Models\UserCollectionItem;
use App\Models\Card;
use App\Models\CardsVariant; // Předpoklad existence modelu a factory pro varianty
use Illuminate\Database\Eloquent\Factories\Factory;

class UserCollectionItemFactory extends Factory
{
    protected $model = UserCollectionItem::class;

    public function definition(): array
    {
        $cardExists = Card::exists();
        // Předpoklad pro variant_id - upravit podle skutečnosti
        $variantExists = class_exists(CardsVariant::class) && CardsVariant::exists();


        return [
            'user_collection_id' => UserCollection::factory(),
            'card_id' => $cardExists ? Card::inRandomOrder()->first()->id : null,
            // 'variant_id' - nutno upřesnit, jak se má generovat
            // Pokud je to cizí klíč na tabulku variants:
            'variant_id' => $variantExists ? CardsVariant::inRandomOrder()->first()->id : $this->faker->optional()->word, // Placeholder
            'notes' => $this->faker->optional()->sentence,
            'order' => $this->faker->numberBetween(1, 100),
        ];
    }

    /*  // Znovu zakomentováno
    public function forPokemon(Pokemon $pokemon): Factory
    {
        return $this->state(function (array $attributes) use ($pokemon) {
            return [
                'pokemon_id' => $pokemon->id,
                'name' => $pokemon->name, // Předpoklad, že model Pokemon má 'name'
                // 'image_url' => $pokemon->sprites['front_default'], // Záleží na struktuře modelu Pokemon
            ];
        });
    }
    */
} 