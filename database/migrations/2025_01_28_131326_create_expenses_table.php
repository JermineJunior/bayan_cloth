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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('madin_id')->constrained('tree4s')->cascadeOnDelete();
            $table->foreignId('cach_id')->nullable()->constrained('tree4s')->cascadeOnDelete();
            $table->foreignId('bank_id')->nullable()->constrained('tree4s')->cascadeOnDelete();
            $table->decimal('cach_name',18,2)->nullable();
            $table->decimal('bank_name',18,2)->nullable();
            $table->text('note')->nullable();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
