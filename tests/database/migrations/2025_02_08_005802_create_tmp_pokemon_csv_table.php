<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTmpPokemonCsvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmp_pokemon_csv', function (Blueprint $table) {
            $table->integer('cardmarket_id')->primary();
            $table->string('name')->nullable();
            $table->string('collector_number', 50)->nullable();
            $table->string('rarity', 50)->nullable();
            $table->string('expansion')->nullable();
            $table->string('expansion_code', 50)->nullable();
            $table->string('scryfall_id', 100)->nullable();
            $table->integer('tcgplayer_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tmp_pokemon_csv');
    }
}
