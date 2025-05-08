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
        Schema::create('cards_variant_types', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->comment('Unikátní kód pro typ varianty (např. 1=Normal, 2=Holo, 3=Reverse)');
            $table->integer('variant')->default(0)->index()->comment('Původní hodnota ze sloupce cards_variant.variant pro mapování');
            $table->string('name')->comment('Název typu varianty (např. Normal, Holo, Reverse Holo)');
            $table->string('price_column_suffix')->nullable()->comment('Suffix cenového sloupce (např. _holo_avg30) nebo celý název sloupce');
            $table->string('description')->nullable();
            
            $table->unique(['code', 'variant']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards_variant_types');
    }
};
