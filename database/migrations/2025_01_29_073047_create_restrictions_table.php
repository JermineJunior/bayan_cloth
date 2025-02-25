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
        Schema::create('restrictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tree4_id')->constrained()->cascadeOnDelete(); 
            $table->bigInteger('tree4_code');
            $table->integer('madin');
            $table->integer('dain');
            $table->date('date');
            $table->text('node')->nullable();
            $table->bigInteger('constant_number');
            $table->tinyInteger('type');
            $table->foreignId('sale_invoice_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_invoice_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('sale_invoice_return_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_invoice_return_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('operation_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restrictions');
    }
};
