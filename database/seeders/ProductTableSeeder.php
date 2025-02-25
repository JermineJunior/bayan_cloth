<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $product = Product::create([
            'barcode' => 123456789,
            'name' => 'بندول',
            'category_id' => 1,
            'purchase_price' => 1500,
            'unit_purchase_price' => 3,
            'reorder_level' => 9,
            'unit_reorder_level' => 3,
            'expire_date' => '2027-12-31',
        ]);

        DB::table('product_units')->insert([
            'product_id' => $product->id,
            'unit_id' => 1,
            'price' => 100,
            'conversion_factor' => 1,
        ]);

        DB::table('product_units')->insert([
            'product_id' => $product->id,
            'unit_id' => 2,
            'price' => 500,
            'conversion_factor' => 4,
        ]);

        DB::table('product_units')->insert([
            'product_id' => $product->id,
            'unit_id' => 3,
            'price' => 2000,
            'conversion_factor' => 30,
        ]);

        DB::table('product_stocks')->insert([
            'product_id' => $product->id,
            'stock_id' => 1,
            'quantity' => 100,
            'unit_stock' => 1,
        ]);

        DB::table('product_stocks')->insert([
            'product_id' => $product->id,
            'stock_id' => 2,
            'quantity' => 5,
            'unit_stock' => 1,
        ]);

        $product = Product::create([
            'barcode' => 12345678,
            'name' => 'مدرر40',
            'category_id' => 1,
            'purchase_price' => 1000,
            'unit_purchase_price' => 3,
            'reorder_level' => 9,
            'unit_reorder_level' => 3,
            'expire_date' => '2027-12-31',
        ]);

        DB::table('product_units')->insert([
            'product_id' => $product->id,
            'unit_id' => 2,
            'price' => 500,
            'conversion_factor' => 1,
        ]);

        DB::table('product_units')->insert([
            'product_id' => $product->id,
            'unit_id' => 3,
            'price' => 1500,
            'conversion_factor' => 3,
        ]);

        DB::table('product_stocks')->insert([
            'product_id' => $product->id,
            'stock_id' => 1,
            'quantity' => 100,
            'unit_stock' => 2,
        ]);

        DB::table('product_stocks')->insert([
            'product_id' => $product->id,
            'stock_id' => 2,
            'quantity' => 10,
            'unit_stock' => 2,
        ]);
    }
}
