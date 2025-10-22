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
       Schema::create('suppliers', function (Blueprint $table) {
    $table->id('Supplier_id');
    $table->string('Supplier_name');
    $table->string('Contact_number')->nullable();
    $table->string('Address')->nullable();
    $table->enum('Status', ['Active', 'Archive'])->default('Active');
    $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
