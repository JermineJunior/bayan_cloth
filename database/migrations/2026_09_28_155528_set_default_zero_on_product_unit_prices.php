<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Give the per-unit product prices a default of 0 so a product can be
     * created before its prices are set. Both columns are already NOT NULL,
     * so NOT NULL is kept and only the default is added.
     */
    public function up(): void
    {
        $this->setPriceDefaults(0);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->setPriceDefaults(null);
    }

    /**
     * Set (or clear) the default on the unit price columns, keeping the
     * existing decimal(10,2) precision and the existing NOT NULL constraint.
     */
    private function setPriceDefaults(?int $default): void
    {
        if (Schema::hasTable('product_units')) {
            // MODIFY keeps the declared type; only the default changes.
            DB::statement(
                'ALTER TABLE product_units
                 MODIFY price DECIMAL(10,2) NOT NULL '.($default === null ? '' : 'DEFAULT '.$default).',
                 MODIFY price_purchase DECIMAL(10,2) NOT NULL '.($default === null ? '' : 'DEFAULT '.$default)
            );
        }

        // The products table has no price columns; handled defensively in
        // case one is ever added under either of these names.
        foreach (['price', 'price_purchase'] as $column) {
            if (Schema::hasTable('products') && Schema::hasColumn('products', $column)) {
                DB::statement(
                    'ALTER TABLE products
                     MODIFY `'.$column.'` DECIMAL(10,2) NOT NULL '.($default === null ? '' : 'DEFAULT '.$default)
                );
            }
        }
    }
};
