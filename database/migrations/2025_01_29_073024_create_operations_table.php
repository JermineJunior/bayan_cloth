<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('madin_id')->nullable()->constrained('tree4s')->cascadeOnDelete();
            $table->foreignId('dain_id')->nullable()->constrained('tree4s')->cascadeOnDelete();
            $table->integer('madin')->nullable();
            $table->integer('dain')->nullable();
            $table->bigInteger('price');
            $table->bigInteger('constant_number');
            $table->text('node')->nullable();
            $table->foreignId('sale_invoice_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_invoice_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('sale_invoice_return_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_invoice_return_id')->nullable()->constrained()->cascadeOnDelete();
            $table->tinyInteger('type');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};
