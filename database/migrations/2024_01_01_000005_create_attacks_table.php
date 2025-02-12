<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attacks', function (Blueprint $table) {
            $table->id();
            $table->string('card_id');
            $table->string('name');
            $table->json('cost');
            $table->integer('converted_energy_cost');
            $table->string('damage')->nullable();
            $table->text('text')->nullable();
            $table->timestamps();

            $table->foreign('card_id')->references('id')->on('cards');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attacks');
    }
}; 