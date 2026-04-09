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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id');
            $table->foreignUuid('product_id');
            $table->foreignUuid('from_warehouse_id')->nullable();
            $table->foreignUuid('to_warehouse_id')->nullable();
            $table->integer('quantity');
            $table->string('movement_type');
            $table->string('reference_number');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movement');
    }
};
