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
        Schema::create('user_wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('card_id', 20)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->integer('variant_id')->nullable();
            $table->enum('priority', ['high', 'medium', 'low'])->default('medium');
            $table->enum('target_condition', ['mint', 'near_mint', 'excellent', 'good', 'played', 'poor'])->default('near_mint');
            $table->decimal('max_price', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexy (včetně unikátního klíče) definujeme zde
            $table->index('card_id');
            $table->index('variant_id');
            $table->unique(['user_id', 'card_id', 'variant_id'], 'uk_user_card_variant');
        });

        // Cizí klíče přidáme v samostatném kroku
        Schema::table('user_wishlists', function (Blueprint $table) {
            $table->foreign('card_id')->references('id')->on('cards')->onDelete('cascade');
            $table->foreign('variant_id')->references('cm_id')->on('cards_variant')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Při rollbacku nejprve odstraníme cizí klíče, pak tabulku
        Schema::table('user_wishlists', function (Blueprint $table) {
            if (Schema::hasColumn('user_wishlists', 'card_id')) {
                try { $table->dropForeign(['card_id']); } catch (\Exception $e) {}
            }
            if (Schema::hasColumn('user_wishlists', 'variant_id')) {
                try { $table->dropForeign(['variant_id']); } catch (\Exception $e) {}
            }
        });
        Schema::dropIfExists('user_wishlists');
    }
};
