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
        Schema::create('sets', function (Blueprint $table) {
            $table->string('id', 20)->primary();
            $table->string('name', 100);
            $table->string('series', 100)->nullable();
            $table->integer('printed_total')->nullable();
            $table->integer('total')->nullable();
            $table->string('ptcgo_code', 10)->nullable();
            $table->date('release_date')->nullable();
            $table->string('symbol_url', 255)->nullable();
            $table->string('logo_url', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sets');
    }
};
