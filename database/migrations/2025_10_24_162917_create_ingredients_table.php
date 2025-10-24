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
            $table->text('Description')->nullable();
            $table->decimal('Quantity', 10, 2)->default(0);
            $table->string('Unit')->nullable(); // kg, liters, pieces, etc.
            $table->foreignId('Supplier_id')
                  ->nullable()
                  ->constrained('suppliers', 'Supplier_id')
                  ->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};