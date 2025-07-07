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
        Schema::create('transactions', function (Blueprint $table) {
              $table->id();
        $table->unsignedBigInteger('customer_id');
        $table->enum('type', ['earned', 'redeemed', 'deducted']);
        $table->integer('points');
        $table->decimal('amount', 10, 2)->nullable();
        $table->text('description')->nullable();
        $table->text('reason')->nullable();
        $table->timestamp('date')->useCurrent();
        $table->timestamps();

        $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
