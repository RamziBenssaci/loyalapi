<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
             $table->id();
        $table->string('full_name');
        $table->string('phone_number')->unique();
        $table->string('email')->nullable();
        $table->enum('gender', ['Male', 'Female'])->nullable();
        $table->string('pin_code');
        $table->integer('points')->default(0);
        $table->string('tier')->default('Bronze');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
