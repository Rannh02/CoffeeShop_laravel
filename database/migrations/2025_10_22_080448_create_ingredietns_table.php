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
        Schema::create('ingredients', function (Blueprint $table) {
        $table->id('Ingredient_id');
        $table->string('Ingredient_name');
        $table->integer('Quantity');
        $table->string('Unit');
        $table->unsignedBigInteger('Supplier_id')->nullable();
        $table->timestamps();

        $table->foreign('Supplier_id')
        ->references('Supplier_id')
        ->on('suppliers')
        ->onDelete('set null');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
