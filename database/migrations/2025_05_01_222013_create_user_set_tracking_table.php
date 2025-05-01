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
        Schema::create('user_set_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('set_id', 20); // Předpokládáme, že tabulka 'sets' existuje
            $table->foreignId('collection_id')->nullable()->constrained('user_collections')->onDelete('set null');
            $table->string('tracking_view', 50);
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->enum('status', ['not_started', 'in_progress', 'completed'])->default('not_started');
            $table->timestamps();

            // Cizí klíč pro set_id
            $table->foreign('set_id')->references('id')->on('sets')->onDelete('cascade'); // Přizpůsobte dle potřeby

            // Unikátní klíč
            $table->unique(['user_id', 'set_id', 'tracking_view'], 'uk_user_set_view');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_set_tracking');
    }
};
