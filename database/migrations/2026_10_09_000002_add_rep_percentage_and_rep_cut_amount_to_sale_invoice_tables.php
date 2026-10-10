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
            $table->decimal('rep_percentage', 5, 2)->default(0);
            $table->decimal('rep_cut_amount', 15, 2)->default(0);
        });

        Schema::table('sale_invoice_returns', function (Blueprint $table) {
            $table->decimal('rep_percentage', 5, 2)->default(0);
            $table->decimal('rep_cut_amount', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_invoices', function (Blueprint $table) {
            $table->dropColumn(['rep_percentage', 'rep_cut_amount']);
        });

        Schema::table('sale_invoice_returns', function (Blueprint $table) {
            $table->dropColumn(['rep_percentage', 'rep_cut_amount']);
        });
    }
};
