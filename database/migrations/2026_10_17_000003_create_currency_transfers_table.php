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
        Schema::create('currency_transfers', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('from_account_id')->constrained('tree4s')->restrictOnDelete();
            $table->foreignId('to_account_id')->constrained('tree4s')->restrictOnDelete();
            $table->foreignId('from_currency_id')->constrained('curenceys')->restrictOnDelete();
            $table->decimal('from_amount', 18, 2);
            $table->foreignId('to_currency_id')->constrained('curenceys')->restrictOnDelete();
            $table->decimal('to_amount', 18, 2);
            $table->decimal('base_amount', 18, 2);
            $table->unsignedBigInteger('constant_number');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_transfers');
    }
};
