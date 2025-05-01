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
        Schema::create('prices_cm', function (Blueprint $table) {
            // V SQL není primární klíč id, ale kombinace (cm_id, updated_at)
            // $table->id(); // Nepoužijeme standardní ID
            $table->integer('cm_id')->default(0);
            $table->dateTime('updated_at');
            $table->string('card_id', 20)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable(); // Explicitní charset a collation
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
            // Timestamps v SQL nejsou

            // Primární klíč
            $table->primary(['cm_id', 'updated_at']);

            // Indexy z SQL
            $table->index('card_id', 'idx_card_id'); // Index pro přidaný sloupec card_id
            $table->index('updated_at'); // idx_updated_at
            $table->index(['card_id', 'updated_at'], 'idx_card_updated'); // Index pro přidaný sloupec card_id

            // Cizí klíč
            $table->foreign('cm_id')->references('cm_id')->on('cards_variant')->onDelete('cascade'); // V SQL bylo jen REFERENCES, přidáno onDelete
            // FK pro přidaný card_id?
            // $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices_cm');
    }
};
