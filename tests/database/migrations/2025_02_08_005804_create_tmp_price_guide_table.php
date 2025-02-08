<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTmpPriceGuideTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmp_price_guide', function (Blueprint $table) {
            $table->integer('id_product');
            $table->integer('id_category')->nullable();
            $table->decimal('avg', 10, 2)->nullable();
            $table->decimal('low', 10, 2)->nullable();
            $table->decimal('trend', 10, 2)->nullable();
            $table->decimal('avg1', 10, 2)->nullable();
            $table->decimal('avg7', 10, 2)->nullable();
            $table->decimal('avg30', 10, 2)->nullable();
            $table->decimal('avg_holo', 10, 2)->nullable();
            $table->decimal('low_holo', 10, 2)->nullable();
            $table->decimal('trend_holo', 10, 2)->nullable();
            $table->decimal('avg1_holo', 10, 2)->nullable();
            $table->decimal('avg7_holo', 10, 2)->nullable();
            $table->decimal('avg30_holo', 10, 2)->nullable();
            $table->timestamps()->default('current_timestamp()');
            
            $table->primary(['id_product', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tmp_price_guide');
    }
}
