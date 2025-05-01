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
        Schema::create('prices_tcg', function (Blueprint $table) {
            $table->id();
            $table->string('card_id', 20)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable();
            $table->enum('price_type', ['normal','holofoil','reverseHolofoil','1stEditionHolofoil','1stEditionNormal'])->nullable();
            $table->decimal('price_low', 10, 2)->nullable();
            $table->decimal('price_mid', 10, 2)->nullable();
            $table->decimal('price_high', 10, 2)->nullable();
            $table->decimal('price_market', 10, 2)->nullable();
            $table->decimal('price_direct_low', 10, 2)->nullable();
            $table->integer('cm_id')->nullable();
            $table->dateTime('updated_at');

            $table->unique(['card_id', 'price_type', 'updated_at'], 'uk_card_price_type_updated');

            $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices_tcg');
    }
};
