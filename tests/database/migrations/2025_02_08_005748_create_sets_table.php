<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sets', function (Blueprint $table) {
            $table->string('id', 20)->primary();
            $table->string('name', 100);
            $table->string('series', 100)->nullable();
            $table->integer('printed_total')->nullable();
            $table->integer('total')->nullable();
            $table->string('ptcgo_code', 10)->nullable();
            $table->date('release_date')->nullable();
            $table->string('symbol_url')->nullable();
            $table->string('logo_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sets');
    }
}
