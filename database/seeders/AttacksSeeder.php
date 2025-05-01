<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AttacksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = 'attacks';
        $jsonPath = database_path('data/attacks.json');

        if (!File::exists($jsonPath)) {
            Log::error("AttacksSeeder: JSON file not found at path: {$jsonPath}");
            $this->command->error("JSON file not found: {$jsonPath}");
            return;
        }

        $jsonContent = File::get($jsonPath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error("AttacksSeeder: Error decoding JSON file: " . json_last_error_msg());
            $this->command->error("Error decoding JSON file: " . json_last_error_msg());
            return;
        }

        if (empty($data)) {
            $this->command->info("AttacksSeeder: No data found in JSON file to seed.");
            return;
        }

        // Vyprázdnění tabulky pro idempotenci
        Schema::disableForeignKeyConstraints();
        DB::table($tableName)->truncate();
        Schema::enableForeignKeyConstraints();
        
        // Zpracování JSON sloupce 'cost'
        $processedData = array_map(function ($item) {
            if (isset($item['cost']) && is_array($item['cost'])) {
                 $item['cost'] = json_encode($item['cost']);
            }
            // Odstranění 'id', pokud existuje v JSON, protože DB má auto-increment
            unset($item['id']); 
            return $item;
        }, $data);


        // Vložení dat
        foreach (array_chunk($processedData, 500) as $chunk) {
             DB::table($tableName)->insert($chunk);
        }

        $this->command->info("AttacksSeeder: Successfully seeded {$tableName} table.");
    }
}
