<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prices_cm', function (Blueprint $table) {
            $table->id();
            $table->string('card_id');
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
            $table->timestamps();

            $table->foreign('card_id')->references('id')->on('cards');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prices_cm');
    }
}; 