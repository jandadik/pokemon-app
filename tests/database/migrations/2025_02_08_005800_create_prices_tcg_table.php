<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricesTcgTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices_tcg', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('card_id', 20)->nullable();
            $table->timestamp('updated_at')->default('current_timestamp()');
            $table->enum('price_type', ['normal', 'holofoil', 'reverseHolofoil', '1stEditionHolofoil', '1stEditionNormal'])->nullable();
            $table->decimal('price_low', 10, 2)->nullable();
            $table->decimal('price_mid', 10, 2)->nullable();
            $table->decimal('price_high', 10, 2)->nullable();
            $table->decimal('price_market', 10, 2)->nullable();
            $table->decimal('price_direct_low', 10, 2)->nullable();
            
            $table->unique(['card_id', 'price_type', 'updated_at'], 'card_price_type');
            $table->foreign('card_id', 'prices_tcg_ibfk_1')->references('id')->on('cards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prices_tcg');
    }
}
