<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = 'cards';
        $jsonPath = database_path('data/cards.json');

        if (!File::exists($jsonPath)) {
            Log::error("CardsSeeder: JSON file not found at path: {$jsonPath}");
            $this->command->error("JSON file not found: {$jsonPath}");
            return;
        }

        $jsonContent = File::get($jsonPath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error("CardsSeeder: Error decoding JSON file: " . json_last_error_msg());
            $this->command->error("Error decoding JSON file: " . json_last_error_msg());
            return;
        }

        if (empty($data)) {
            $this->command->info("CardsSeeder: No data found in JSON file to seed.");
            return;
        }

        // Vyprázdnění tabulky pro idempotenci
        Schema::disableForeignKeyConstraints();
        DB::table($tableName)->truncate();
        Schema::enableForeignKeyConstraints();
        
        // JSON sloupce je potřeba převést zpět na JSON string pro DB::insert
        $processedData = array_map(function ($item) {
            foreach (['types', 'subtypes', 'rules', 'evolves_to', 'abilities', 'weaknesses', 'resistances', 'retreat_cost', 'ancient_trait', 'legalities'] as $jsonColumn) {
                if (isset($item[$jsonColumn]) && is_array($item[$jsonColumn])) {
                    $item[$jsonColumn] = json_encode($item[$jsonColumn]);
                }
            }
            return $item;
        }, $data);

        // Vložení dat
        foreach (array_chunk($processedData, 500) as $chunk) {
             DB::table($tableName)->insert($chunk);
        }

        $this->command->info("CardsSeeder: Successfully seeded {$tableName} table.");
    }
}
