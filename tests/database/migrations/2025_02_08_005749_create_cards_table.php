<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->string('id', 20)->primary();
            $table->string('set_id', 20);
            $table->string('name', 100)->index('idx_card_name');
            $table->string('supertype', 50)->index('idx_card_type');
            $table->string('number', 10)->nullable();
            $table->string('ptcgo_code', 10)->nullable();
            $table->longText('types')->nullable();
            $table->longText('subtypes')->nullable();
            $table->longText('rules')->nullable();
            $table->string('rarity', 50)->nullable();
            $table->char('regulation_mark', 1)->nullable();
            $table->integer('hp')->nullable();
            $table->integer('national_pokedex_number')->nullable();
            $table->string('evolves_from', 100)->nullable();
            $table->longText('evolves_to')->nullable();
            $table->longText('abilities')->nullable();
            $table->longText('weaknesses')->nullable();
            $table->longText('resistances')->nullable();
            $table->longText('retreat_cost')->nullable();
            $table->integer('converted_retreat_cost')->nullable();
            $table->longText('ancient_trait')->nullable();
            $table->text('flavor_text')->nullable();
            $table->string('illustrator', 100)->nullable();
            $table->longText('legalities')->nullable();
            $table->string('img_small')->nullable();
            $table->string('img_file_small')->nullable();
            $table->string('img_large')->nullable();
            $table->string('img_file_large')->nullable();
            $table->string('url_tcgplayer')->nullable();
            $table->string('url_cardmarket')->nullable();
            $table->timestamps()->default('current_timestamp()');
            
            $table->index(['ptcgo_code', 'number'], 'idx_ptcgo_search');
            $table->foreign('set_id', 'cards_ibfk_1')->references('id')->on('sets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cards');
    }
}
