<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Status;
use App\Models\Admin;
use App\Models\EquipmentCategory;
use App\Models\Equipment;

class MainSeeder extends Seeder
{
    public function run(): void
    {
        // ── Statuses ─────────────────────────────────────────────────
        // Keeping your original status names so existing logic still maps
        foreach (['Pending', 'Approved', 'Returned', 'Cancelled'] as $name) {
            Status::firstOrCreate(['status_name' => $name]);
        }

        // ── Default Admin Account ─────────────────────────────────────
        // Mirrors your old hardcoded admin: admin@um.edu.ph / admin123
        Admin::firstOrCreate(
            ['email' => 'admin@um.edu.ph'],
            [
                'first_name' => 'PE',
                'last_name'  => 'Admin',
                'password'   => Hash::make('admin123'),
            ]
        );

        // ── Equipment Categories ──────────────────────────────────────
        $categories = [
            'Sports Equipment',
            'Audio/Visual Equipment',
            'Laboratory Equipment',
            'Computing Equipment',
            'Outdoor/Field Equipment',
        ];

        foreach ($categories as $name) {
            EquipmentCategory::firstOrCreate(['category_name' => $name]);
        }

        // ── Sample Equipment ──────────────────────────────────────────
        // Mirrors your old EquipmentSeeder so you don't lose that data
        $admin    = Admin::where('email', 'admin@um.edu.ph')->first();
        $category = EquipmentCategory::where('category_name', 'Sports Equipment')->first();

        $items = [
            ['equipment_name' => 'Basketball', 'total_quantity' => 20],
            ['equipment_name' => 'Volleyball',  'total_quantity' => 15],
        ];

        foreach ($items as $item) {
            Equipment::firstOrCreate(
                ['equipment_name' => $item['equipment_name']],
                [
                    'category_id'        => $category->category_id,
                    'admin_id'           => $admin->admin_id,
                    'total_quantity'     => $item['total_quantity'],
                    'available_quantity' => $item['total_quantity'],
                ]
            );
        }
    }
}
