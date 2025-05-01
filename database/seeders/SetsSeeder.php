<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File; // Pro čtení souboru
use Illuminate\Support\Facades\Log; // Pro logování chyb
use Illuminate\Support\Facades\Schema; // Pro kontrolu klíčů

class SetsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = 'sets';
        $jsonPath = database_path('data/sets.json');

        if (!File::exists($jsonPath)) {
            Log::error("SetsSeeder: JSON file not found at path: {$jsonPath}");
            $this->command->error("JSON file not found: {$jsonPath}");
            return;
        }

        $jsonContent = File::get($jsonPath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error("SetsSeeder: Error decoding JSON file: " . json_last_error_msg());
            $this->command->error("Error decoding JSON file: " . json_last_error_msg());
            return;
        }

        if (empty($data)) {
            $this->command->info("SetsSeeder: No data found in JSON file to seed.");
            return;
        }

        // Vyprázdnění tabulky pro idempotenci
        Schema::disableForeignKeyConstraints();
        DB::table($tableName)->truncate();
        Schema::enableForeignKeyConstraints();

        // Vložení dat - insert je rychlejší než create pro velké objemy
        // Rozdělení na chunky pro případ velmi velkých souborů (např. po 500 záznamech)
        foreach (array_chunk($data, 500) as $chunk) {
             DB::table($tableName)->insert($chunk);
        }

        $this->command->info("SetsSeeder: Successfully seeded {$tableName} table.");
    }
}
