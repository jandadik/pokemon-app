<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserCollection;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserCollectionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserCollection::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Automatically creates a user if not provided
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->optional()->sentence,
            'is_public' => $this->faker->boolean,
            'is_default' => false, // Default to false, handle true case specifically in tests/service logic
        ];
    }

    /**
     * Indicate that the collection is public.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function public(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_public' => true,
            ];
        });
    }

    /**
     * Indicate that the collection is private.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function private(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_public' => false,
            ];
        });
    }

    /**
     * Indicate that the collection is default.
     * Note: is_default should usually be handled carefully to ensure only one default per user.
     * This factory state is for convenience in tests where this rule might be tested or bypassed.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function default(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_default' => true,
            ];
        });
    }
} 