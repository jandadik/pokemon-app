<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// use Illuminate\Support\Facades\DB; // Už není potřeba

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cards_variant', function (Blueprint $table) {
            // V SQL je cm_id INT(11) NOT NULL PRIMARY KEY
            // Není auto-increment!
            $table->integer('cm_id')->primary();
            // Definujeme card_id rovnou jako VARCHAR(20) s collation
            $table->string('card_id', 20)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable();
            $table->integer('cm_expansion_id')->nullable();
            $table->integer('cm_metacard_id')->nullable();
            $table->dateTime('date_added')->nullable();
            $table->string('collector_number', 50)->nullable();
            $table->string('ptcgo_code', 50)->nullable();
            $table->integer('tcgplayer_id')->nullable();
            $table->string('rarity', 50)->nullable();
            $table->boolean('variant_normal')->default(false);
            $table->boolean('variant_holo')->default(false);
            $table->boolean('variant_reverse')->default(false);
            $table->boolean('variant_promo')->default(false);
            $table->integer('variant')->default(0); // Co tento sloupec znamená?
            $table->boolean('variant_pokeball')->default(false);
            $table->boolean('variant_masterball')->default(false);
            $table->boolean('is_primary_variant')->default(false);
            // Timestamps v SQL nejsou

            // Indexy z SQL
            $table->index('card_id'); // idx_card_id
            $table->index('cm_metacard_id', 'idx_cm_metacard');
            $table->index('ptcgo_code', 'idx_ptcgo_code');
            $table->index(['card_id', 'is_primary_variant'], 'idx_cards_variant_primary');

            // Cizí klíč pro card_id
            $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade'); // FK_cards_card_id v SQL měl jen REFERENCES
        });
        
        // DB::statement(...) už není potřeba

        // Trigger - vynechávám, řeší unikátnost is_primary_variant = 1 per card_id
        // Tuto logiku bude třeba zajistit aplikačně.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards_variant');
    }
}; 