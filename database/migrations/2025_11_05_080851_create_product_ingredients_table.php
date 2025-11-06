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
    Schema::create('product_ingredients', function (Blueprint $table) {
        $table->id('Product_ingredient_id');

        // Define foreign keys properly (no duplicates)
        $table->foreignId('Product_id')
              ->constrained('products', 'Product_id') // assumes your products table has Product_id as PK
              ->onDelete('cascade');

        $table->foreignId('Ingredient_id')
              ->constrained('ingredients', 'Ingredient_id') // assumes your ingredients table has Ingredient_id as PK
              ->onDelete('cascade');

        $table->decimal('Quantity_used', 10, 2);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_ingredients');
    }
};
