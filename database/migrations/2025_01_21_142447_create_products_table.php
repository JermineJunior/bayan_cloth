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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('barcode')->unique();
            $table->string('name')->unique();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->decimal('purchase_price',10,2)->nullable();
            $table->tinyInteger('unit_purchase_price');
            $table->integer('reorder_level')->nullable()->nullable();
            $table->tinyInteger('unit_reorder_level');
            $table->date('expire_date')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
