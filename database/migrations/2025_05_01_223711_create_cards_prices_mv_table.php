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
        // Vytváříme jako tabulku, i když název naznačuje Materialized View
        Schema::create('cards_prices_mv', function (Blueprint $table) {
            $table->string('card_id', 20)->charset('utf8mb4')->collation('utf8mb4_unicode_ci'); // Shoda s cards.id
            $table->string('card_name', 100);
            $table->string('set_id', 20); // Shoda se sets.id
            $table->integer('variant_id')->nullable(); // Shoda s cards_variant.cm_id
            $table->decimal('tcg_price_low', 10, 2)->nullable();
            $table->decimal('tcg_price_mid', 10, 2)->nullable();
            $table->decimal('tcg_price_high', 10, 2)->nullable();
            $table->decimal('tcg_price_market', 10, 2)->nullable();
            $table->decimal('tcg_price_direct_low', 10, 2)->nullable();
            $table->enum('tcg_price_type', ['normal','holofoil','reverseHolofoil','1stEditionHolofoil','1stEditionNormal'])->nullable();
            $table->dateTime('tcg_updated_at')->nullable();
            $table->decimal('cm_price_low', 10, 2)->nullable();
            $table->decimal('cm_price_trend', 10, 2)->nullable();
            $table->decimal('cm_price_avg', 10, 2)->nullable();
            $table->decimal('cm_price_suggested', 10, 2)->nullable();
            $table->decimal('cm_avg7', 10, 2)->nullable();
            $table->decimal('cm_avg30', 10, 2)->nullable();
            $table->dateTime('cm_updated_at')->nullable();
            $table->timestamp('last_updated')->useCurrent(); // DEFAULT current_timestamp()
            $table->decimal('cm_reverse_holo_sell', 10, 2)->nullable();
            $table->decimal('cm_reverse_holo_low', 10, 2)->nullable();
            $table->decimal('cm_reverse_holo_trend', 10, 2)->nullable();
            $table->decimal('cm_reverse_holo_avg1', 10, 2)->nullable();
            $table->decimal('cm_reverse_holo_avg7', 10, 2)->nullable();
            $table->decimal('cm_reverse_holo_avg30', 10, 2)->nullable();
            $table->decimal('tcg_reverse_holo_price_low', 10, 2)->nullable();
            $table->decimal('tcg_reverse_holo_price_mid', 10, 2)->nullable();
            $table->decimal('tcg_reverse_holo_price_high', 10, 2)->nullable();
            $table->decimal('tcg_reverse_holo_price_market', 10, 2)->nullable();
            $table->decimal('tcg_reverse_holo_price_direct_low', 10, 2)->nullable();
            $table->dateTime('tcg_reverse_holo_updated_at')->nullable();

            // Primární klíč z SQL dumpu?
            // V SQL je PK card_id. Ale card_id nemusí být unikátní, pokud máme ceny pro různé varianty?
            // Pro jistotu použijeme kombinaci card_id a variant_id jako primární klíč, pokud variant_id není null?
            // Nebo prostě nastavíme card_id jako primární klíč, jak je v SQL.
            $table->primary('card_id');

            // Indexy z SQL
            $table->index('set_id', 'idx_set_id');
            $table->index('variant_id', 'idx_variant_id');
            $table->index('tcg_price_market', 'idx_price_tcg_market');
            $table->index('cm_price_trend', 'idx_price_cm_trend');

            // Cizí klíče? Mohou způsobovat problémy s výkonem u MV/cache tabulek.
            // Prozatím je nepřidáváme, ale je to k zvážení.
            // $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');
            // $table->foreign('set_id')->references('id')->on('sets')->onDelete('cascade');
            // $table->foreign('variant_id')->references('cm_id')->on('cards_variant')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards_prices_mv');
    }
};
