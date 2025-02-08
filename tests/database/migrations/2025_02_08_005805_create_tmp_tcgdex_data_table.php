<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTmpTcgdexDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmp_tcgdex_data', function (Blueprint $table) {
            $table->string('id', 50)->primary();
            $table->string('image')->nullable();
            $table->tinyInteger('variant_firstEdition')->nullable();
            $table->tinyInteger('variant_holo')->nullable();
            $table->tinyInteger('variant_normal')->nullable();
            $table->tinyInteger('variant_reverse')->nullable();
            $table->tinyInteger('variant_wPromo')->nullable();
            $table->timestamp('created_at')->default('current_timestamp()');
            $table->string('status', 50)->nullable();
            $table->string('id_set', 50)->nullable();
            $table->string('id_card', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tmp_tcgdex_data');
    }
}
