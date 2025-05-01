<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Pro raw SQL (trigger)

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            // V SQL je id VARCHAR(20) NOT NULL PRIMARY KEY
            $table->string('id', 20)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->primary();
            $table->string('set_id', 20);
            $table->string('name', 100);
            $table->string('supertype', 50);
            $table->integer('number')->nullable(); // Plněno triggerem nebo manuálně
            $table->string('number_txt', 10)->nullable();
            $table->string('ptcgo_code', 10)->nullable();
            $table->json('types')->nullable();
            $table->json('subtypes')->nullable();
            $table->json('rules')->nullable();
            $table->string('rarity', 50)->nullable();
            $table->char('regulation_mark', 1)->nullable();
            $table->integer('hp')->nullable();
            $table->integer('national_pokedex_number')->nullable();
            $table->string('evolves_from', 100)->nullable();
            $table->json('evolves_to')->nullable();
            $table->json('abilities')->nullable();
            $table->json('weaknesses')->nullable();
            $table->json('resistances')->nullable();
            $table->json('retreat_cost')->nullable();
            $table->integer('converted_retreat_cost')->nullable();
            $table->json('ancient_trait')->nullable();
            $table->text('flavor_text')->nullable();
            $table->string('illustrator', 100)->nullable();
            $table->json('legalities')->nullable();
            $table->string('img_small', 255)->nullable();
            $table->string('img_file_small', 255)->nullable();
            $table->string('img_large', 255)->nullable();
            $table->string('img_file_large', 255)->nullable();
            $table->string('url_tcgplayer', 255)->nullable();
            $table->string('url_cardmarket', 255)->nullable();
            $table->integer('primary_variant_id')->nullable(); // FK bude přidán později po vytvoření cards_variant
            $table->timestamps(); // V SQL jsou

            // Indexy z původního SQL
            $table->index('set_id'); // idx_set_id, idx_card_set
            $table->index('supertype'); // idx_supertype, idx_card_type
            $table->index('rarity'); // idx_rarity, idx_card_rarity
            $table->index('name'); // idx_card_name, idx_name
            $table->index('number'); // idx_number
            $table->index('primary_variant_id', 'idx_cards_primary_variant');
            $table->index(['set_id', 'number'], 'idx_set_number'); // idx_set_number, idx_card_ordering
            $table->index(['ptcgo_code', 'number'], 'idx_ptcgo_search');
            
            // Fulltext index (používáme number_txt)
            $table->fullText(['name', 'number_txt'], 'idx_search'); // idx_search, idx_card_search

            // Cizí klíč pro set_id (předpokládá, že tabulka sets už existuje)
            $table->foreign('set_id')->references('id')->on('sets')->onDelete('cascade');
        });

        // Přidání triggerů (pokud jsou stále potřeba)
        // DB::unprepared('CREATE TRIGGER ...'); // Zde by byly definice triggerů
        // Prozatím triggery vynechávám, mohou způsobovat problémy s Eloquentem
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Je potřeba odstranit triggery před smazáním tabulky, pokud byly vytvořeny
        // DB::unprepared('DROP TRIGGER IF EXISTS before_insert_cards');
        // DB::unprepared('DROP TRIGGER IF EXISTS before_update_cards');
        Schema::dropIfExists('cards');
    }
}; 