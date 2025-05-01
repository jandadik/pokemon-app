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
        Schema::create('cards_variants_prices_mv', function (Blueprint $table) {
            $table->integer('cm_id')->primary(); // V SQL je PK
            $table->string('card_id', 20)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(); // Shoda s cards.id
            $table->integer('cm_expansion_id')->nullable();
            $table->integer('cm_metacard_id')->nullable();
            $table->string('collector_number', 50)->nullable();
            $table->string('ptcgo_code', 50)->nullable();
            $table->string('rarity', 50)->nullable();
            $table->boolean('variant_normal')->nullable();
            $table->boolean('variant_holo')->nullable();
            $table->boolean('variant_reverse')->nullable();
            $table->boolean('variant_promo')->nullable();
            $table->integer('variant')->nullable();
            $table->boolean('variant_pokeball')->nullable();
            $table->boolean('variant_masterball')->nullable();
            $table->boolean('is_primary_variant')->nullable();
            $table->decimal('average_sell_price', 10, 2)->nullable();
            $table->decimal('low_price', 10, 2)->nullable();
            $table->decimal('trend_price', 10, 2)->nullable();
            $table->decimal('german_pro_low', 10, 2)->nullable();
            $table->decimal('suggested_price', 10, 2)->nullable();
            $table->decimal('reverse_holo_sell', 10, 2)->nullable();
            $table->decimal('reverse_holo_low', 10, 2)->nullable();
            $table->decimal('reverse_holo_trend', 10, 2)->nullable();
            $table->decimal('low_price_ex_plus', 10, 2)->nullable();
            $table->decimal('avg1', 10, 2)->nullable();
            $table->decimal('avg7', 10, 2)->nullable();
            $table->decimal('avg30', 10, 2)->nullable();
            $table->decimal('reverse_holo_avg1', 10, 2)->nullable();
            $table->decimal('reverse_holo_avg7', 10, 2)->nullable();
            $table->decimal('reverse_holo_avg30', 10, 2)->nullable();
            $table->dateTime('cm_updated_at')->nullable();
            $table->timestamp('last_updated')->useCurrent();

            // Indexy z SQL
            $table->index('card_id', 'idx_card_id');
            $table->index('collector_number', 'idx_collector_number');
            $table->index('trend_price', 'idx_trend_price');
            $table->index(['variant_normal','variant_holo','variant_reverse'], 'idx_variant_type');

            // Cizí klíče? Spíše ne pro MV/cache.
            // $table->foreign('cm_id')->references('cm_id')->on('cards_variant')->onDelete('cascade');
            // $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards_variants_prices_mv');
    }
};
