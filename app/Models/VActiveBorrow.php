<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VActiveBorrow extends Model
{
    protected $table = 'v_active_borrows';
    public $timestamps = false;
    protected $primaryKey = 'borrow_record_id';

    protected $casts = [
        'borrow_date'          => 'date',
        'expected_return_date' => 'date',
    ];

    public static function call(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_active_borrows");
        DB::statement("
            CREATE VIEW v_active_borrows AS
            SELECT
                br.borrow_record_id,
                br.borrow_date,
                br.check_out_time,
                br.expected_return_date,
                CONCAT(b.first_name, ' ', b.last_name) AS borrower_name,
                b.email                                 AS borrower_email,
                b.contact_number,
                e.equipment_name,
                bre.quantity_borrowed,
                ec.category_name,
                s.status_name,
                CONCAT(a.first_name, ' ', a.last_name)  AS processed_by
            FROM borrow_records br
            JOIN borrowers              b   ON br.borrower_id  = b.borrower_id
            JOIN admins                 a   ON br.admin_id     = a.admin_id
            JOIN statuses               s   ON br.status_id    = s.status_id
            JOIN borrow_record_equipment bre ON br.borrow_record_id = bre.borrow_record_id
            JOIN equipment              e   ON bre.equipment_id = e.equipment_id
            JOIN equipment_categories   ec  ON e.category_id   = ec.category_id
            WHERE br.borrow_record_id NOT IN (
                SELECT borrow_record_id FROM return_records
            )
            AND s.status_name IN ('Pending', 'Approved')
        ");
    }

    /**
     * Get the distinct borrow_record_ids that are currently active.
     * Used in DashboardController to cross-reference with BorrowRecord.
     */
    public static function activeIds(): array
    {
        return static::query()
            ->select('borrow_record_id')
            ->distinct()
            ->pluck('borrow_record_id')
            ->toArray();
    }
}
