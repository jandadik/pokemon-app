<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attacks', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('card_id', 20);
            $table->string('name', 100);
            $table->longText('cost')->nullable();
            $table->string('damage', 20)->nullable();
            $table->text('text')->nullable();
            $table->integer('converted_energy_cost')->nullable();
            
            $table->foreign('card_id', 'attacks_ibfk_1')->references('id')->on('cards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attacks');
    }
}
