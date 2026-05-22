<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VEquipmentInventory extends Model
{
    protected $table = 'v_equipment_inventory';
    public $timestamps = false;
    protected $primaryKey = 'equipment_id';

    public static function call(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_equipment_inventory");
        DB::statement("
            CREATE VIEW v_equipment_inventory AS
            SELECT
                e.equipment_id,
                e.equipment_name,
                ec.category_name,
                ec.category_id,
                e.total_quantity,
                e.available_quantity,
                e.total_quantity - e.available_quantity AS quantity_out,
                CASE
                    WHEN e.available_quantity = 0                              THEN 'Out of Stock'
                    WHEN e.available_quantity < e.total_quantity * 0.25        THEN 'Low Stock'
                    ELSE 'Available'
                END                                     AS availability_status,
                CONCAT(a.first_name, ' ', a.last_name)  AS managed_by,
                a.email                                 AS admin_email
            FROM equipment e
            JOIN equipment_categories ec ON e.category_id = ec.category_id
            JOIN admins               a  ON e.admin_id    = a.admin_id
        ");
    }

    /**
     * Apply the same search / category / sort filters the admin
     * EquipmentController currently uses, but reading from the view.
     */
    public static function filtered(
        ?string $search,
        ?string $categoryId,
        ?string $sort
    ): \Illuminate\Support\Collection {
        $query = static::query();

        if ($search) {
            $query->where('equipment_name', 'like', "%{$search}%");
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        match ($sort) {
            'name_desc'     => $query->orderBy('equipment_name', 'desc'),
            'stock_high'    => $query->orderBy('available_quantity', 'desc'),
            'stock_low'     => $query->orderBy('available_quantity', 'asc'),
            'category_asc'  => $query->orderBy('category_name', 'asc'),
            'category_desc' => $query->orderBy('category_name', 'desc'),
            default         => $query->orderBy('equipment_name', 'asc'),
        };

        return $query->get();
    }
}
