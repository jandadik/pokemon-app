<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vytváříme jako tabulku (materialized view implementace)
        // Kombinuje data z cards_variant a cards_variant_types
        // Cílem je zrychlit endpoint catalog.cards.variants
        Schema::create('cards_variants_types_mv', function (Blueprint $table) {
            $table->id('mv_id');
            
            // Identifikátory karty a varianty
            $table->string('card_id', 20)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->integer('cm_id');
            
            // Informace o typu varianty
            $table->integer('variant_type_code');
            $table->string('variant_type_name');
            $table->string('variant_type_description')->nullable();
            $table->string('price_column_suffix')->nullable();
            
            // Původní variant ID pro spojení
            $table->integer('variant');
            
            // Další metadata z cards_variant
            $table->integer('cm_expansion_id')->nullable();
            $table->integer('cm_metacard_id')->nullable();
            $table->string('collector_number', 50)->nullable();
            $table->string('ptcgo_code', 50)->nullable();
            $table->integer('tcgplayer_id')->nullable();
            $table->string('rarity', 50)->nullable();
            $table->dateTime('date_added')->nullable();
            
            // Boolean flags pro typ varianty
            $table->boolean('variant_normal')->default(false);
            $table->boolean('variant_holo')->default(false);
            $table->boolean('variant_reverse')->default(false);
            $table->boolean('variant_promo')->default(false);
            $table->boolean('variant_pokeball')->default(false);
            $table->boolean('variant_masterball')->default(false);
            
            // Označení primární varianty
            $table->boolean('is_primary_variant')->default(false);
            
            // Timestamp pro tracking aktualizací
            $table->timestamp('last_updated')->useCurrent();
            
            // Indexy pro rychlé dotazování
            $table->index('card_id', 'idx_mv_card_id');
            $table->index('variant_type_code', 'idx_mv_variant_type_code');
            $table->index(['card_id', 'variant_type_code'], 'idx_mv_card_variant');
            $table->index('cm_id', 'idx_mv_cm_id');
            $table->index('is_primary_variant', 'idx_mv_primary');
            $table->index(['card_id', 'is_primary_variant'], 'idx_mv_card_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards_variants_types_mv');
    }
};
