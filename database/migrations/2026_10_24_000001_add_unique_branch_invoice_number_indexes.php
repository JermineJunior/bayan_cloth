<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'sale_invoices',
        'sale_invoice_returns',
        'purchase_invoices',
        'purchase_invoice_returns',
        'preliminary_invoices',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            $duplicates = DB::table($table)
                ->select('branch_id', 'invoice_number', DB::raw('COUNT(*) as total'))
                ->groupBy('branch_id', 'invoice_number')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            if ($duplicates->isNotEmpty()) {
                $details = $duplicates
                    ->map(fn ($row) => "{$table}: branch_id={$row->branch_id}, invoice_number={$row->invoice_number}")
                    ->implode('; ');

                throw new RuntimeException('توجد أرقام فواتير مكررة داخل نفس الفرع: '.$details);
            }
        }

        foreach (['sale_invoices', 'purchase_invoices'] as $table) {
            $oldIndex = $table.'_invoice_number_unique';
            if (Schema::hasIndex($table, $oldIndex)) {
                Schema::table($table, function (Blueprint $blueprint) use ($oldIndex) {
                    $blueprint->dropUnique($oldIndex);
                });
            }
        }

        foreach ($this->tables as $table) {
            $index = $table.'_branch_id_invoice_number_unique';
            if (! Schema::hasIndex($table, $index)) {
                Schema::table($table, function (Blueprint $blueprint) use ($index) {
                    $blueprint->unique(['branch_id', 'invoice_number'], $index);
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            $index = $table.'_branch_id_invoice_number_unique';
            if (Schema::hasIndex($table, $index)) {
                Schema::table($table, function (Blueprint $blueprint) use ($index) {
                    $blueprint->dropUnique($index);
                });
            }
        }
    }
};
