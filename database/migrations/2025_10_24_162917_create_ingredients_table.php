<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id('Ingredient_id');
            $table->string('Ingredient_name');
            $table->string('Unit')->nullable(); // kg, liters, pieces, etc.
            $table->decimal('StockQuantity', 10, 2)->default(0);
            $table->integer('ReorderLevel')->default(10); 
            
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};