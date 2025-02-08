<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_logs', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->timestamp('timestamp')->default('current_timestamp()');
            $table->string('operation_type', 50);
            $table->string('status', 20);
            $table->string('item_id', 50)->nullable();
            $table->string('item_name', 100)->nullable();
            $table->text('message')->nullable();
            $table->text('error_details')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('import_logs');
    }
}
