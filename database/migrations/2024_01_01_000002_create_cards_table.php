<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('set_id');
            $table->string('name');
            $table->string('supertype');
            $table->string('number');
            $table->string('ptcgo_code')->nullable();
            $table->json('types')->nullable();
            $table->json('subtypes')->nullable();
            $table->json('rules')->nullable();
            $table->string('rarity')->nullable();
            $table->string('regulation_mark')->nullable();
            $table->integer('hp')->nullable();
            $table->integer('national_pokedex_number')->nullable();
            $table->string('evolves_from')->nullable();
            $table->json('evolves_to')->nullable();
            $table->json('abilities')->nullable();
            $table->json('weaknesses')->nullable();
            $table->json('resistances')->nullable();
            $table->json('retreat_cost')->nullable();
            $table->integer('converted_retreat_cost')->nullable();
            $table->json('ancient_trait')->nullable();
            $table->text('flavor_text')->nullable();
            $table->string('illustrator')->nullable();
            $table->json('legalities')->nullable();
            $table->string('img_small')->nullable();
            $table->string('img_file_small')->nullable();
            $table->string('img_large')->nullable();
            $table->string('img_file_large')->nullable();
            $table->string('url_tcgplayer')->nullable();
            $table->string('url_cardmarket')->nullable();
            $table->timestamps();

            $table->foreign('set_id')->references('id')->on('sets');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
}; 