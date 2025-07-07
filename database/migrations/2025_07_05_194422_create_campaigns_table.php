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
        Schema::create('campaigns', function (Blueprint $table) {
             $table->id();
        $table->string('name');
        $table->text('description')->nullable();
        $table->date('start_date');
        $table->date('end_date');
        $table->string('earn_rate');
        $table->string('redeem_rate');
        $table->enum('status', ['Active', 'Inactive'])->default('Active');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
