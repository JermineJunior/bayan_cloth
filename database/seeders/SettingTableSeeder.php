<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create(attributes: [
            'name' => 'pos bayan',
            'location' => 'khartoum',
            'phone' => '0121000000',
            'email' => 'bayan@gmail.com',
            'logo' => '17307014763895.png',
        ]);
    }
}
