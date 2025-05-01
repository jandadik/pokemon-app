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
        Schema::create('maintenance_log', function (Blueprint $table) {
            $table->id();
            $table->string('operation', 100);
            $table->text('details')->nullable();
            $table->integer('affected_rows')->nullable();
            $table->timestamp('timestamp')->useCurrent();

            $table->index('operation', 'idx_operation');
            $table->index('timestamp', 'idx_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_log');
    }
};
