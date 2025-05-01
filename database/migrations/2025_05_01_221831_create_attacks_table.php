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
        Schema::create('attacks', function (Blueprint $table) {
            $table->id();
            $table->string('card_id', 20)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('name', 100);
            $table->json('cost')->nullable();
            $table->string('damage', 20)->nullable();
            $table->text('text')->nullable();
            $table->integer('converted_energy_cost')->nullable();

            $table->index('card_id');

            $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attacks');
    }
};
