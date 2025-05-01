<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// Základní seedery
use Database\Seeders\RoleSeeder;
use Database\Seeders\SetTrackingRuleSeeder;
// Seedery pro katalog
use Database\Seeders\SetsSeeder;
use Database\Seeders\CardsSeeder;
use Database\Seeders\CardsVariantSeeder;
use Database\Seeders\AttacksSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Vytvoření testovacího uživatele (můžete zakomentovat, pokud nepotřebujete)
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            // Nejprve základní data aplikace
            RoleSeeder::class,
            
            // Poté katalog karet (pořadí je důležité!)
            SetsSeeder::class,
            CardsSeeder::class,
            CardsVariantSeeder::class,
            AttacksSeeder::class,
            
            // Nakonec data specifická pro sbírky (závisí na katalogu)
            SetTrackingRuleSeeder::class,
            
            // další seedery...
        ]);
    }
}
