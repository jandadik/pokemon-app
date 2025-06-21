<?php

namespace Database\Factories;

use App\Models\Set;
use Illuminate\Database\Eloquent\Factories\Factory;

class SetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Set::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Vytvoření unikátního ID pro set, např. kombinace písmen a čísel
        // Příklad: 'swsh11', 'sv01' atd. Délka se může lišit.
        $ptcgoCode = $this->faker->unique()->bothify('?##??'); // např. a12bc, z09xy - upravte podle potřeby

        return [
            'id' => $ptcgoCode, // Použijeme ptcgo_code jako ID, pokud je to konvence
            'name' => $this->faker->words(2, true) . ' Expansion',
            'series' => $this->faker->randomElement(['Sword & Shield', 'Scarlet & Violet', 'Sun & Moon', 'XY']),
            'printed_total' => $this->faker->numberBetween(100, 280),
            'total' => $this->faker->numberBetween(120, 300),
            'ptcgo_code' => $ptcgoCode, // Stejné jako ID pro konzistenci
            'release_date' => $this->faker->date(),
            'symbol_url' => $this->faker->imageUrl(50, 50, 'abstract'),
            'logo_url' => $this->faker->imageUrl(100, 40, 'abstract'),
            // timestamps jsou false v modelu, takže je zde neuvádíme
        ];
    }
} 