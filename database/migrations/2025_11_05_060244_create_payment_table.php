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
        Schema::create('payment', function (Blueprint $table) {
            $table->id('Payment_id');
            $table->unsignedBigInteger('Order_id');
            $table->string('PaymentMethod')->nullable();
            $table->decimal('AmountPaid', 10, 2);
            $table->dateTime('PaymentDate')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('TransactionReference')->nullable();
            $table->foreign('Order_id')
                ->references('Order_id')
                ->on('orders')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
