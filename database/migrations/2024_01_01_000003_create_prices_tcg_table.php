<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prices_tcg', function (Blueprint $table) {
            $table->id();
            $table->string('card_id');
            $table->decimal('price_low', 10, 2)->nullable();
            $table->decimal('price_mid', 10, 2)->nullable();
            $table->decimal('price_high', 10, 2)->nullable();
            $table->decimal('price_market', 10, 2)->nullable();
            $table->decimal('price_direct_low', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('card_id')->references('id')->on('cards');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prices_tcg');
    }
}; 