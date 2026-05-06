<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Equipment::create([
            'equipment_name' => 'Basketball',
            'total_quantity' => 20,
            'available_quantity' => 20,
        ]);

        \App\Models\Equipment::create([
            'equipment_name' => 'Volleyball',
            'total_quantity' => 15,
            'available_quantity' => 15,
        ]);
    }
}
