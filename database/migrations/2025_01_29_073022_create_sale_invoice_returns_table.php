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
        Schema::create('sale_invoice_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('tree4s')->cascadeOnDelete();
            $table->foreignId('sale_invoice_id')->nullable()->constrained()->cascadeOnDelete();
            $table->integer('sale_invoice_number')->nullable();
            $table->date('invoice_date'); // تاريخ الفاتورة
            $table->foreignId('stock_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->unique(); // رقم الفاتورة
            $table->decimal('total_amount', 15, 2); // المبلغ الإجمالي
            $table->decimal('sub_total_amount', 15, 2); // المبلغ الإجمالي قبل الضرائب
            $table->decimal('tax_amount', 15, 2); // قيمة الضريبة
            $table->decimal('discount_amount', 15, 2)->default(0); // قيمة الخصم
            $table->tinyInteger('payment_status'); // حالة الدفع (قيد الانتظار، مدفوعة، متأخرة)
            $table->tinyInteger('status')->default(1); // حالة الفاتورة (مفعلة، ملغاة، مسودة)
            $table->tinyInteger('payment_method')->nullable(); // طريقة الدفع (مثل: نقدي، بطاقة ائتمان، تحويل بنكي)
            $table->tinyInteger('bank_id')->nullable();
            $table->tinyInteger('cash_id')->nullable();
            $table->decimal('cash_amount', 15, 2)->nullable();
            $table->decimal('bank_amount', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_invoice_returns');
    }
};
