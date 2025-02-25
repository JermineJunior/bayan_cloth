<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionTableSeeder::class);
        $this->call(SettingTableSeeder::class); 
        $this->call(UserTableSeeder::class); 
        $this->call(CategoryTableSeeder::class); 
        $this->call(UnitTableSeeder::class); 
        $this->call(StockTableSeeder::class); 
        $this->call(Tree1TableSeeder::class); 
        $this->call(Tree2TableSeeder::class); 
        $this->call(Tree3TableSeeder::class); 
        $this->call(Tree4TableSeeder::class); 
        $this->call(BankTableSeeder::class);
        $this->call(SafeTableSeeder::class);
        $this->call(ProductTableSeeder::class);
    } 
}
