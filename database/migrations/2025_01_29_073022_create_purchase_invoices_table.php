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
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->nullable()->constrained('tree4s')->cascadeOnDelete();
            $table->foreignId('representative_id')->nullable()->constrained('tree4s')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->date('date'); // تاريخ الفاتورة
            $table->bigInteger('invoice_number'); // رقم الفاتورة
            $table->foreignId('stock_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_amount', 15, 2); // المبلغ الإجمالي
            $table->decimal('sub_total_amount', 15, 2); // المبلغ الإجمالي قبل الضرائب
            $table->decimal('tax_amount', 15, 2); // قيمة الضريبة
            $table->tinyInteger('tax_status')->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0)->nullable(); // قيمة الخصم
            $table->tinyInteger('status')->default(1); // حالة الفاتورة (مفعلة، ملغاة، مسودة)
            $table->integer('bank_name')->nullable();
            $table->integer('cash_name')->nullable();
            $table->decimal('cash_amount_total', 15, 2)->nullable();
            $table->decimal('bank_amount_total', 15, 2)->nullable();
            $table->bigInteger('process_no')->nullable();
            $table->decimal('deferend_amount',15,2)->nullable();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('invoice_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoices');
    }
};
