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
        Schema::create('employee', function (Blueprint $table) {
            $table->id('Employee_id');
            $table->string('First_name', 100);
            $table->string('Last_name', 100);
            $table->string('Cashier_Account', 100)->unique();
            $table->string('Password', 255);
            $table->enum('Gender',['Male', 'Female']);
            $table->integer('Contact_number');
            $table->dateTime('Date/Time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('Status', ['Active', 'Archived'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee');
    }
};
