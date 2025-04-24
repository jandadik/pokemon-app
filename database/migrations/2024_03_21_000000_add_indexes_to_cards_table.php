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
        // Přidání B-tree indexů
        Schema::table('cards', function (Blueprint $table) {
            // Základní indexy pro filtrování
            $table->index('set_id', 'idx_set_id');
            $table->index('supertype', 'idx_supertype');
            $table->index('rarity', 'idx_rarity');
            
            // Indexy pro řazení
            $table->index('number', 'idx_number');
            $table->index('name', 'idx_name');
            
            // Kompozitní index
            $table->index(['set_id', 'number'], 'idx_set_number');
            
            // Fulltext index pro vyhledávání
            $table->fullText(['name', 'number'], 'idx_search');
        });

        // Přidání chybějících indexů do tabulky cards
        Schema::table('cards', function (Blueprint $table) {
            // Index pro filtrování podle rarity (chybí v aktuální struktuře)
            $table->index('rarity', 'idx_card_rarity');
            
            // Fulltext index pro vyhledávání v názvu a čísle
            $table->fullText(['name', 'number'], 'idx_card_search');
        });

        // Přidání indexů pro tabulku prices_cm
        Schema::table('prices_cm', function (Blueprint $table) {
            // Index pro card_id (pokud neexistuje)
            if (!Schema::hasIndex('prices_cm', 'idx_card_id')) {
                $table->index('card_id', 'idx_card_id');
            }
            
            // Index pro updated_at (pokud neexistuje)
            if (!Schema::hasIndex('prices_cm', 'idx_updated_at')) {
                $table->index('updated_at', 'idx_updated_at');
            }
            
            // Kompozitní index pro card_id a updated_at
            if (!Schema::hasIndex('prices_cm', 'idx_card_updated')) {
                $table->index(['card_id', 'updated_at'], 'idx_card_updated');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Odstranění indexů z tabulky cards
        Schema::table('cards', function (Blueprint $table) {
            $table->dropIndex('idx_set_id');
            $table->dropIndex('idx_supertype');
            $table->dropIndex('idx_rarity');
            $table->dropIndex('idx_number');
            $table->dropIndex('idx_name');
            $table->dropIndex('idx_set_number');
            $table->dropFullText('idx_search');
            $table->dropIndex('idx_card_rarity');
            $table->dropFullText('idx_card_search');
        });

        // Odstranění přidaných indexů z tabulky prices_cm
        Schema::table('prices_cm', function (Blueprint $table) {
            if (Schema::hasIndex('prices_cm', 'idx_card_id')) {
                $table->dropIndex('idx_card_id');
            }
            if (Schema::hasIndex('prices_cm', 'idx_updated_at')) {
                $table->dropIndex('idx_updated_at');
            }
            if (Schema::hasIndex('prices_cm', 'idx_card_updated')) {
                $table->dropIndex('idx_card_updated');
            }
        });
    }
}; 