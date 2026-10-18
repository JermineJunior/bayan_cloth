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
        if (Schema::hasColumn('tree4s', 'rep_percentage')) {
            Schema::table('tree4s', function (Blueprint $table) {
                $table->dropColumn('rep_percentage');
            });
        }

        if (Schema::hasColumn('sale_invoices', 'rep_percentage') || Schema::hasColumn('sale_invoices', 'rep_cut_amount')) {
            Schema::table('sale_invoices', function (Blueprint $table) {
                foreach (['rep_percentage', 'rep_cut_amount'] as $column) {
                    if (Schema::hasColumn('sale_invoices', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasColumn('sale_invoice_returns', 'rep_percentage') || Schema::hasColumn('sale_invoice_returns', 'rep_cut_amount')) {
            Schema::table('sale_invoice_returns', function (Blueprint $table) {
                foreach (['rep_percentage', 'rep_cut_amount'] as $column) {
                    if (Schema::hasColumn('sale_invoice_returns', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasColumn('revenues', 'rep_percentage') || Schema::hasColumn('revenues', 'rep_cut_amount')) {
            Schema::table('revenues', function (Blueprint $table) {
                foreach (['rep_percentage', 'rep_cut_amount'] as $column) {
                    if (Schema::hasColumn('revenues', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tree4s', function (Blueprint $table) {
            if (!Schema::hasColumn('tree4s', 'rep_percentage')) {
                $table->decimal('rep_percentage', 5, 2)->nullable()->default(0);
            }
        });

        Schema::table('sale_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_invoices', 'rep_percentage')) {
                $table->decimal('rep_percentage', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('sale_invoices', 'rep_cut_amount')) {
                $table->decimal('rep_cut_amount', 15, 2)->default(0);
            }
        });

        Schema::table('sale_invoice_returns', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_invoice_returns', 'rep_percentage')) {
                $table->decimal('rep_percentage', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('sale_invoice_returns', 'rep_cut_amount')) {
                $table->decimal('rep_cut_amount', 15, 2)->default(0);
            }
        });

        Schema::table('revenues', function (Blueprint $table) {
            if (!Schema::hasColumn('revenues', 'rep_percentage')) {
                $table->decimal('rep_percentage', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('revenues', 'rep_cut_amount')) {
                $table->decimal('rep_cut_amount', 15, 2)->default(0);
            }
        });
    }
};
