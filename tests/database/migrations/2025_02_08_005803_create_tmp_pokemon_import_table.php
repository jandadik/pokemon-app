<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTmpPokemonImportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmp_pokemon_import', function (Blueprint $table) {
            $table->integer('id_product')->primary();
            $table->string('card_id')->nullable();
            $table->string('name')->nullable()->index('idx_tmp_name');
            $table->integer('id_category')->nullable()->index('idx_tmp_category');
            $table->string('category_name', 100)->nullable();
            $table->integer('id_expansion')->nullable()->index('idx_tmp_expansion');
            $table->integer('id_metacard')->nullable();
            $table->dateTime('date_added')->nullable();
            $table->string('namePt')->nullable();
            $table->string('collector_number', 50)->nullable();
            $table->string('rarity', 50)->nullable();
            $table->string('expansion')->nullable();
            $table->string('expansion_code', 50)->nullable();
            $table->string('scryfall_id', 100)->nullable();
            $table->integer('tcgplayer_id')->nullable();
            $table->timestamp('created_at')->default('current_timestamp()');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tmp_pokemon_import');
    }
}
