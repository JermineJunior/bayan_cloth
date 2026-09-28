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
        Schema::table('restrictions', function (Blueprint $table) {
            $table->string('transaction_no', 100)->nullable()->after('constant_number');
        });

        Schema::table('sale_invoices', function (Blueprint $table) {
            $table->string('transaction_no', 100)->nullable()->after('bank_amount_total');
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->string('transaction_no', 100)->nullable()->after('bank_amount_total');
        });

        Schema::table('sale_invoice_returns', function (Blueprint $table) {
            $table->string('transaction_no', 100)->nullable()->after('bank_amount_total');
        });

        Schema::table('purchase_invoice_returns', function (Blueprint $table) {
            $table->string('transaction_no', 100)->nullable()->after('bank_amount_total');
        });

        Schema::table('revenues', function (Blueprint $table) {
            $table->string('transaction_no', 100)->nullable()->after('bank_name');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->string('transaction_no', 100)->nullable()->after('bank_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restrictions', function (Blueprint $table) {
            $table->dropColumn('transaction_no');
        });

        Schema::table('sale_invoices', function (Blueprint $table) {
            $table->dropColumn('transaction_no');
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropColumn('transaction_no');
        });

        Schema::table('sale_invoice_returns', function (Blueprint $table) {
            $table->dropColumn('transaction_no');
        });

        Schema::table('purchase_invoice_returns', function (Blueprint $table) {
            $table->dropColumn('transaction_no');
        });

        Schema::table('revenues', function (Blueprint $table) {
            $table->dropColumn('transaction_no');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('transaction_no');
        });
    }
};