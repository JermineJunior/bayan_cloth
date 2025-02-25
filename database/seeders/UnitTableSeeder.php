<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'حبة'],
            ['name' => 'شريط'],
            ['name' => 'صندوق'],
            ['name' => 'علبة'],
            ['name' => 'درب'],
            ['name' => 'كرتونة'],
            ['name' => 'زجاجة']
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
        
    }
}
