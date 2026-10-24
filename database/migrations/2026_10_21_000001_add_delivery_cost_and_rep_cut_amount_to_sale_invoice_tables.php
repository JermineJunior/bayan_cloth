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
        Schema::table('sale_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_invoices', 'delivery_cost')) {
                $table->decimal('delivery_cost', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('sale_invoices', 'rep_cut_amount')) {
                $table->decimal('rep_cut_amount', 15, 2)->default(0);
            }
        });

        Schema::table('sale_invoice_returns', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_invoice_returns', 'delivery_cost')) {
                $table->decimal('delivery_cost', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('sale_invoice_returns', 'rep_cut_amount')) {
                $table->decimal('rep_cut_amount', 15, 2)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_invoices', function (Blueprint $table) {
            foreach (['delivery_cost', 'rep_cut_amount'] as $column) {
                if (Schema::hasColumn('sale_invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('sale_invoice_returns', function (Blueprint $table) {
            foreach (['delivery_cost', 'rep_cut_amount'] as $column) {
                if (Schema::hasColumn('sale_invoice_returns', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
