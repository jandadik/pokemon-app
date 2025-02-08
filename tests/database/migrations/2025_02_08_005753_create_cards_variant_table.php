<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsVariantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards_variant', function (Blueprint $table) {
            $table->integer('cm_id')->primary();
            $table->string('card_id')->nullable();
            $table->integer('cm_expansion_id')->nullable();
            $table->integer('cm_metacard_id')->nullable()->index('idx_cm_metacard');
            $table->dateTime('date_added')->nullable();
            $table->string('collector_number', 50)->nullable();
            $table->string('ptcgo_code', 50)->nullable()->index('idx_ptcgo_code');
            $table->integer('tcgplayer_id')->nullable();
            $table->string('rarity', 50)->nullable();
            $table->boolean('variant_normal')->default(0);
            $table->boolean('variant_holo')->default(0);
            $table->boolean('variant_reverse')->default(0);
            $table->boolean('variant_promo')->default(0);
            $table->integer('variant')->default(0);
            $table->boolean('variant_pokeball')->default(0);
            $table->boolean('variant_masterball')->default(0);
            
            $table->foreign('card_id', 'fk_cards_card_id')->references('id')->on('cards');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cards_variant');
    }
}
