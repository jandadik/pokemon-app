<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PricesCmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = 'prices_cm';
        $jsonPath = database_path('data/prices_cm.json');

        if (!File::exists($jsonPath)) {
            Log::error("PricesCmSeeder: JSON file not found at path: {$jsonPath}");
            $this->command->error("JSON file not found: {$jsonPath}");
            return;
        }

        $jsonContent = File::get($jsonPath);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error("PricesCmSeeder: Error decoding JSON file: " . json_last_error_msg());
            $this->command->error("Error decoding JSON file: " . json_last_error_msg());
            return;
        }

        if (empty($data)) {
            $this->command->info("PricesCmSeeder: No data found in JSON file to seed.");
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table($tableName)->truncate();
        Schema::enableForeignKeyConstraints();

        foreach (array_chunk($data, 500) as $chunk) {
            DB::table($tableName)->insert($chunk);
        }

        $this->command->info("PricesCmSeeder: Successfully seeded {$tableName} table.");
    }
} 