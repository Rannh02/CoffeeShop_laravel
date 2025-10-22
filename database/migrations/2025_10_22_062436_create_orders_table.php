<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('Order_id');
            $table->unsignedBigInteger('Customer_id')->nullable();
            $table->unsignedBigInteger('Employee_id')->nullable();
            $table->string('Customer_name');
            $table->dateTime('Order_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->decimal('TotalAmount', 10, 2);
            $table->enum('Order_Type', ['Dine In', 'Takeout']);
            $table->timestamps();

            $table->foreign('Customer_id')
                ->references('Customer_id')
                ->on('customer')
                ->onDelete('set null');

            $table->foreign('Employee_id')
                ->references('Employee_id')
                ->on('employee')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
