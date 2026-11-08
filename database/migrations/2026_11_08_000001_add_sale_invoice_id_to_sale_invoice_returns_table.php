<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_invoice_returns', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_invoice_returns', 'sale_invoice_id')) {
                $table->foreignId('sale_invoice_id')->nullable()->constrained('sale_invoices')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sale_invoice_returns', function (Blueprint $table) {
            if (Schema::hasColumn('sale_invoice_returns', 'sale_invoice_id')) {
                $table->dropConstrainedForeignId('sale_invoice_id');
            }
        });
    }
};
