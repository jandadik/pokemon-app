<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\CardsVariantType; // Import modelu

class CardsVariantTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('data/card_variant_types.json'); // Předpokládaná cesta
        
        if (!File::exists($jsonPath)) {
            $this->command->error("JSON file not found at: {$jsonPath}");
            return;
        }

        $jsonData = File::get($jsonPath);
        $types = json_decode($jsonData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error("Error decoding JSON: " . json_last_error_msg());
            return;
        }

        if (empty($types)) {
            $this->command->info("No variant types found in JSON file or file is empty.");
            return;
        }

        // Vyprázdnění tabulky před seedováním (volitelné)
        DB::table('cards_variant_types')->truncate();

        $this->command->info("Seeding card variant types...");
        $progressBar = $this->command->getOutput()->createProgressBar(count($types));
        $progressBar->start();

        foreach ($types as $typeData) {
            try {
                // Použití modelu pro vložení s mass assignment
                CardsVariantType::create([
                    'code' => $typeData['code'] ?? null,
                    'variant' => $typeData['variant'] ?? null,
                    'name' => $typeData['name'] ?? 'Unknown',
                    'price_column_suffix' => $typeData['price_column_suffix'] ?? null,
                    'description' => $typeData['description'] ?? null,
                ]);
            } catch (\Exception $e) {
                 $this->command->error("\nError seeding type: " . ($typeData['name'] ?? json_encode($typeData)) . " - " . $e->getMessage());
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->info("\nCard variant types seeding completed.");
    }
}
