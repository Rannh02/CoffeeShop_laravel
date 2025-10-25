<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee', function (Blueprint $table) {
            $table->id('Employee_id');
            $table->string('First_name', 50);
            $table->string('Last_name', 50);
            $table->string('Cashier_Account', 100)->unique();
            $table->string('Password', 255);
            $table->enum('Gender', ['Male', 'Female']);
            $table->string('Contact_number', 15);
            $table->enum('Position', ['Cashier', 'Manager', 'Staff']);
            $table->date('Date_of_Hire');
            $table->enum('Status', ['Active', 'Archived'])->default('Active');
            $table->timestamps(); // This creates 'created_at' and 'updated_at'
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee');
    }
};