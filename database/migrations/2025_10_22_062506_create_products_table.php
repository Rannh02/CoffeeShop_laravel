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
        Schema::create('products', function (Blueprint $table) {
            $table->id('Product_id');
            $table->string('Product_name', 150);
            $table->unsignedBigInteger('Category_id')->nullable();
            $table->decimal('Price', 10, 2);
            $table->string('Image_url', 250)->nullable();
            $table->timestamps();

            $table->foreign('Category_id')
                ->references('Category_id')
                ->on('categories')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
