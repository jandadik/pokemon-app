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
        Schema::create('user_collection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained('user_collections')->onDelete('cascade');
            $table->string('card_id', 20)->charset('utf8mb4')->collation('utf8mb4_unicode_ci'); // Explicitní charset a collation
            $table->integer('variant_id')->nullable(); // Opraveno: integer místo unsignedInteger
            $table->integer('quantity')->default(1);
            $table->enum('condition', ['mint', 'near_mint', 'excellent', 'good', 'played', 'poor'])->default('near_mint');
            $table->string('language', 10)->default('en');
            $table->boolean('is_foil')->default(false);
            $table->boolean('is_first_edition')->default(false);
            $table->boolean('is_graded')->default(false);
            $table->string('grade_company', 50)->nullable();
            $table->string('grade_value', 10)->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->date('purchase_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('location', 100)->nullable();
            $table->timestamps();

            // Cizí klíče - předpokládáme existenci tabulek 'cards' a 'cards_variant'
            // V SQL je card_id VARCHAR(20) a variant_id INT(11)
            $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade'); // Přizpůsobte název sloupce/tabulky podle reality
            $table->foreign('variant_id')->references('cm_id')->on('cards_variant')->onDelete('set null'); // Přizpůsobte název sloupce/tabulky podle reality

            // Unikátní klíč
            // Původní: UNIQUE KEY uk_collection_card_variant (collection_id, card_id, variant_id, condition, is_foil)
            // Poznámka: variant_id může být NULL, což může způsobovat problémy s unique constrainty v některých DB. Zjednodušíme?
            // Prozatím přidáme unikátní klíč bez variant_id, pokud je to přijatelné.
            // Nebo jej ponecháme tak, jak je, s vědomím možných NULL problémů.
            // $table->unique(['collection_id', 'card_id', 'variant_id', 'condition', 'is_foil'], 'uk_collection_card_variant_cond_foil');
            // Unikátní klíč s povolením NULL pro variant_id - toto je závislé na DB, Laravel to přímo nepodporuje.
            // V MySQL lze mít NULL v unique indexu vícekrát.
            // Zkusíme definovat standardní unique, MySQL by to mělo zvládnout.
            $table->unique(['collection_id', 'card_id', 'variant_id', 'condition', 'is_foil'], 'uk_collection_card_variant_details');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_collection_items');
    }
};
