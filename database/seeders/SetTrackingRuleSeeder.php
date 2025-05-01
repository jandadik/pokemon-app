<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\SetTrackingRule;

class SetTrackingRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Doporučeno pro zajištění idempotence seederu
        Schema::disableForeignKeyConstraints();
        SetTrackingRule::truncate();
        Schema::enableForeignKeyConstraints();

        $rules = [
            // Base set view
            ['tracking_view' => 'base', 'rarity' => 'Common', 'variant_type' => 'normal', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'base', 'rarity' => 'Common', 'variant_type' => 'reverse', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'base', 'rarity' => 'Uncommon', 'variant_type' => 'normal', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'base', 'rarity' => 'Uncommon', 'variant_type' => 'reverse', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'base', 'rarity' => 'Rare', 'variant_type' => 'holo', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'base', 'rarity' => 'Rare', 'variant_type' => 'reverse', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'base', 'rarity' => 'Double Rare', 'variant_type' => 'holo', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],

            // Master set view (base + secret)
            ['tracking_view' => 'master', 'rarity' => 'Common', 'variant_type' => 'normal', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'master', 'rarity' => 'Common', 'variant_type' => 'reverse', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'master', 'rarity' => 'Uncommon', 'variant_type' => 'normal', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'master', 'rarity' => 'Uncommon', 'variant_type' => 'reverse', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'master', 'rarity' => 'Rare', 'variant_type' => 'holo', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'master', 'rarity' => 'Rare', 'variant_type' => 'reverse', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'master', 'rarity' => 'Double Rare', 'variant_type' => 'holo', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'master', 'rarity' => 'Illustration Rare', 'variant_type' => 'holo', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'master', 'rarity' => 'Special Illustration Rare', 'variant_type' => 'holo', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'master', 'rarity' => 'Hyper Rare', 'variant_type' => 'holo', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            // V SQL bylo Secret Rare jako FALSE, TRUE - předpokládám, že má patřit do master setu
            ['tracking_view' => 'master', 'rarity' => 'Secret Rare', 'variant_type' => 'holo', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => false, 'include_above_printed' => true],

            // Standard set view
            ['tracking_view' => 'standard', 'rarity' => 'Common', 'variant_type' => 'normal', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'standard', 'rarity' => 'Uncommon', 'variant_type' => 'normal', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'standard', 'rarity' => 'Rare', 'variant_type' => 'holo', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'standard', 'rarity' => 'Double Rare', 'variant_type' => 'holo', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'standard', 'rarity' => 'Illustration Rare', 'variant_type' => 'holo', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'standard', 'rarity' => 'Special Illustration Rare', 'variant_type' => 'holo', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'standard', 'rarity' => 'Hyper Rare', 'variant_type' => 'holo', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'standard', 'rarity' => 'Secret Rare', 'variant_type' => 'holo', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => false, 'include_above_printed' => true],

            // Parallel set view
            ['tracking_view' => 'parallel', 'rarity' => 'Common', 'variant_type' => 'reverse', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'parallel', 'rarity' => 'Uncommon', 'variant_type' => 'reverse', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'parallel', 'rarity' => 'Rare', 'variant_type' => 'reverse', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],

            // Normal set view
            ['tracking_view' => 'normal', 'rarity' => 'Common', 'variant_type' => 'normal', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'normal', 'rarity' => 'Uncommon', 'variant_type' => 'normal', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],

            // Normal holo set view
            ['tracking_view' => 'normal_holo', 'rarity' => 'Rare', 'variant_type' => 'holo', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'normal_holo', 'rarity' => 'Double Rare', 'variant_type' => 'holo', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'normal_holo', 'rarity' => 'Illustration Rare', 'variant_type' => 'holo', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'normal_holo', 'rarity' => 'Special Illustration Rare', 'variant_type' => 'holo', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'normal_holo', 'rarity' => 'Hyper Rare', 'variant_type' => 'holo', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'normal_holo', 'rarity' => 'Secret Rare', 'variant_type' => 'holo', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => false, 'include_above_printed' => true],

            // Reverse holo set view
            ['tracking_view' => 'reverse_holo', 'rarity' => 'Common', 'variant_type' => 'reverse', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'reverse_holo', 'rarity' => 'Uncommon', 'variant_type' => 'reverse', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'reverse_holo', 'rarity' => 'Rare', 'variant_type' => 'reverse', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],

            // Pokeball holo set view (pouze pro určité sety)
            ['tracking_view' => 'pokeball_holo', 'rarity' => 'Common', 'variant_type' => 'pokeball', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'pokeball_holo', 'rarity' => 'Uncommon', 'variant_type' => 'pokeball', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'pokeball_holo', 'rarity' => 'Rare', 'variant_type' => 'pokeball', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],

            // Masterball holo set view (pouze pro určité sety)
            ['tracking_view' => 'masterball_holo', 'rarity' => 'Common', 'variant_type' => 'masterball', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'masterball_holo', 'rarity' => 'Uncommon', 'variant_type' => 'masterball', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
            ['tracking_view' => 'masterball_holo', 'rarity' => 'Rare', 'variant_type' => 'masterball', 'series_from' => 'Scarlet & Violet', 'include_in_printed_range' => true, 'include_above_printed' => false],
        ];

        // Vložení dat pomocí modelu pro využití Eloquent událostí a mass assignment
        foreach ($rules as $rule) {
            SetTrackingRule::create($rule);
        }

        // Alternativně pomocí DB fasády (rychlejší pro velké množství dat)
        // DB::table('set_tracking_rules')->insert($rules);
    }
}
