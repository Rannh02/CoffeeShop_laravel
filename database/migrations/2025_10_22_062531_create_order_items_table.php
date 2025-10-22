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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id('OrderItem_id');
            $table->unsignedBigInteger('Order_id')->nullable();
            $table->unsignedBigInteger('Product_id')->nullable();
            $table->integer('Quantity');
            $table->decimal('UnitPrice', 10, 2);
            $table->timestamps();

            $table->foreign('Order_id')
                ->references('Order_id')
                ->on('orders')
                ->onDelete('set null');
            
            $table->foreign('Product_id')
                ->references('Product_id') 
                ->on('products')
                ->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
