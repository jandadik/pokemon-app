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
        Schema::create('set_tracking_rules', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_view', 50);
            $table->string('rarity', 50);
            $table->enum('variant_type', ['normal', 'holo', 'reverse', 'pokeball', 'masterball']);
            $table->string('series_from', 100)->nullable();
            $table->string('series_to', 100)->nullable();
            $table->boolean('include_in_printed_range')->default(true);
            $table->boolean('include_above_printed')->default(false);
            $table->timestamps();

            $table->unique(['tracking_view', 'rarity', 'variant_type', 'series_from'], 'uk_tracking_rule');

            $table->index(['series_from', 'series_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('set_tracking_rules');
    }
};
