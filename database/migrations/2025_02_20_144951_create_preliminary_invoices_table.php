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
        Schema::create('preliminary_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('tree4s')->cascadeOnDelete();
            $table->date('invoice_date'); // تاريخ الفاتورة
            $table->string('invoice_number')->unique(); // رقم الفاتورة
            $table->foreignId('stock_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_amount', 15, 2); // المبلغ الإجمالي
            $table->decimal('sub_total_amount', 15, 2); // المبلغ الإجمالي قبل الضرائب
            $table->decimal('tax_amount', 15, 2); // قيمة الضريبة
            $table->decimal('discount_amount', 15, 2)->default(0); // قيمة الخصم
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preliminary_invoices');
    }
};
