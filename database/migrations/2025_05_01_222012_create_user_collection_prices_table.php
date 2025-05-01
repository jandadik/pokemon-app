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
        Schema::create('user_collection_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained('user_collections')->onDelete('cascade');
            $table->decimal('total_market_value', 10, 2)->nullable();
            $table->decimal('total_purchase_value', 10, 2)->nullable();
            $table->integer('total_cards')->nullable();
            $table->date('snapshot_date');
            $table->timestamps();

            $table->index(['collection_id', 'snapshot_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_collection_prices');
    }
};
