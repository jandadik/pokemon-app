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
        Schema::create('user_set_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User reference
            $table->string('set_id', 20);
            $table->string('card_id', 20)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->integer('variant_id')->nullable();
            $table->string('tracking_view', 50);
            $table->enum('status', ['pending', 'owned', 'skipped'])->default('pending');
            $table->foreignId('collection_item_id')->nullable()->constrained('user_collection_items')->onDelete('set null');
            $table->timestamps();

            // Cizí klíče
            $table->foreign('set_id')->references('id')->on('sets')->onDelete('cascade');
            $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');
            $table->foreign('variant_id')->references('cm_id')->on('cards_variant')->onDelete('set null');
            // Poznámka: Cizí klíč pro user_id už je definován pomocí constrained(), ale SQL ho explicitně uvádí.
            // Můžeme přidat explicitní, pokud je to preferováno, ale constrained() je standard Laravelu.
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Duplikuje constrained()

            // Unikátní klíč
            // Původní: UNIQUE KEY uk_user_set_card_variant (user_id, set_id, card_id, variant_id, tracking_view)
            // Podobně jako u items, variant_id může být NULL. MySQL by to mělo zvládnout.
            $table->unique(['user_id', 'set_id', 'card_id', 'variant_id', 'tracking_view'], 'uk_user_set_card_variant_view');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_set_cards');
    }
};
