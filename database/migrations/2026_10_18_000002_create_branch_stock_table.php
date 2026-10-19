<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('branch_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('stock_id')->constrained('stocks')->cascadeOnDelete();
            $table->unique(['branch_id', 'stock_id']);
        });

        // attach every existing stock to every existing branch (behavior unchanged until admin edits assignments)
      /*   $branches = DB::table('branches')->pluck('id');
        $stocks = DB::table('stocks')->pluck('id');
        $rows = [];
        foreach ($branches as $branchId) {
            foreach ($stocks as $stockId) {
                $rows[] = ['branch_id' => $branchId, 'stock_id' => $stockId];
            }
        }
        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('branch_stock')->insert($chunk);
        } */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_stock');
    }
};
