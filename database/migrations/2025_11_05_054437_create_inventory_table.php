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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id('Inventory_id');
            $table->unsignedBigInteger('Product_id');
            $table->integer('QuantityInStock')->default(0);
            $table->integer('ReorderLevel')->default(10);
            $table->dateTime('LastRestockDate')->nullable();
            $table->timestamps();
            
            $table->foreign('Product_id')
                ->references('Product_id')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
