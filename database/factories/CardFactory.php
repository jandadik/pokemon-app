<?php

namespace Database\Factories;

use App\Models\Card;
use App\Models\Set;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Card::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $set = Set::factory()->create(); // Zajistí, že karta má vždy existující set_id
        $isTrainerGallery = $this->faker->boolean(10); // 10% šance, že je to Trainer Gallery karta
        $maxNumber = $set->printed_total ?? 200;

        if ($isTrainerGallery) {
            $numberInSet = $this->faker->numberBetween(1, 30);
            $numberTxt = 'TG' . str_pad((string)$numberInSet, 2, '0', STR_PAD_LEFT);
        } else {
            $numberInSet = $this->faker->numberBetween(1, $maxNumber);
            $numberTxt = str_pad((string)$numberInSet, 3, '0', STR_PAD_LEFT);
        }

        // Vytvoření ID karty, např. kombinace set_id a čísla karty (nebo TG čísla)
        $cardIdSuffix = $isTrainerGallery ? $numberTxt : $numberTxt; // Může být stejné
        $cardId = $set->id . '-' . $cardIdSuffix;

        return [
            'id' => $cardId, 
            'set_id' => $set->id,
            'name' => $this->faker->words(2, true) . ' ' . $this->faker->randomElement(['V', 'VMAX', 'GX', 'EX', 'Radiant', 'Amazing', '']), // Přidány další typy pro variabilitu
            'supertype' => $this->faker->randomElement(['Pokémon', 'Trainer', 'Energy']),
            'number' => $numberInSet, // Toto musí být INTEGER
            'number_txt' => $numberTxt, // Toto je STRING, např. '001', '101/100', 'TG01'
            'ptcgo_code' => strtoupper($set->id) . ' ' . $numberInSet, // Příklad: 'RCL 43'
            'types' => json_encode($this->faker->randomElements(['Grass', 'Fire', 'Water', 'Lightning', 'Psychic', 'Fighting', 'Darkness', 'Metal', 'Fairy', 'Dragon', 'Colorless'], $this->faker->numberBetween(1, 2))),
            'rarity' => $this->faker->randomElement(['Common', 'Uncommon', 'Rare', 'Rare Holo', 'Ultra Rare', 'Secret Rare', 'Trainer Gallery Holo Rare']),
            'hp' => $this->faker->optional(0.8, null)->numberBetween(30, 340),
            'img_small' => $this->faker->slug(2) . '_small.png', // Jednodušší generování názvu souboru
            'img_large' => $this->faker->slug(2) . '_large.png',
        ];
    }
} 