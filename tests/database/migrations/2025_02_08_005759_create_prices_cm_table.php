<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricesCmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices_cm', function (Blueprint $table) {
            $table->string('card_id', 20)->index('idx_card_id');
            $table->decimal('average_sell_price', 10, 2)->nullable();
            $table->decimal('low_price', 10, 2)->nullable();
            $table->decimal('trend_price', 10, 2)->nullable();
            $table->decimal('german_pro_low', 10, 2)->nullable();
            $table->decimal('suggested_price', 10, 2)->nullable();
            $table->decimal('reverse_holo_sell', 10, 2)->nullable();
            $table->decimal('reverse_holo_low', 10, 2)->nullable();
            $table->decimal('reverse_holo_trend', 10, 2)->nullable();
            $table->decimal('low_price_ex_plus', 10, 2)->nullable();
            $table->decimal('avg1', 10, 2)->nullable();
            $table->decimal('avg7', 10, 2)->nullable();
            $table->decimal('avg30', 10, 2)->nullable();
            $table->decimal('reverse_holo_avg1', 10, 2)->nullable();
            $table->decimal('reverse_holo_avg7', 10, 2)->nullable();
            $table->decimal('reverse_holo_avg30', 10, 2)->nullable();
            $table->timestamp('updated_at')->default('current_timestamp()');
            $table->integer('cm_id')->default(0);
            
            $table->primary(['cm_id', 'updated_at']);
            $table->foreign('cm_id', 'fk_prices_cm_cards_variant')->references('cm_id')->on('cards_variant');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prices_cm');
    }
}
