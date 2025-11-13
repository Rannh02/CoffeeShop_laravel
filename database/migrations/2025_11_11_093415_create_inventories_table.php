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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id('Inventory_id');
            $table->unsignedBigInteger('Product_id');
            $table->unsignedBigInteger('Ingredient_id');
            $table->decimal('QuantityUsed', 10, 2)->default(0);
            $table->decimal('RemainingStock', 10, 2)->nullable();
            $table->enum('Action', ['add', 'deduct'])->default('deduct'); // ✅ Added this
            $table->timestamp('DateUsed')->nullable();
            $table->timestamps();

            $table->foreign('Product_id')
                ->references('Product_id')
                ->on('products')
                ->onDelete('cascade');

            $table->foreign('Ingredient_id')
                ->references('Ingredient_id')
                ->on('ingredients')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
