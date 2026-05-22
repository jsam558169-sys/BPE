<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VOverdueBorrow extends Model
{
    protected $table = 'v_overdue_borrows';
    public $timestamps = false;
    protected $primaryKey = 'borrow_record_id';

    protected $casts = [
        'borrow_date'          => 'date',
        'expected_return_date' => 'date',
    ];

    public static function call(): void
    {
        DB::statement("DROP VIEW IF EXISTS v_overdue_borrows");
        DB::statement("
            CREATE VIEW v_overdue_borrows AS
            SELECT
                br.borrow_record_id,
                br.borrow_date,
                br.expected_return_date,
                DATEDIFF(CURDATE(), br.expected_return_date) AS days_overdue,
                CONCAT(b.first_name, ' ', b.last_name)       AS borrower_name,
                b.email                                       AS borrower_email,
                b.contact_number,
                GROUP_CONCAT(
                    e.equipment_name, ' (x', bre.quantity_borrowed, ')'
                    ORDER BY e.equipment_name
                    SEPARATOR ', '
                )                                             AS borrowed_items,
                CONCAT(a.first_name, ' ', a.last_name)        AS processed_by
            FROM borrow_records br
            JOIN borrowers               b   ON br.borrower_id      = b.borrower_id
            JOIN admins                  a   ON br.admin_id          = a.admin_id
            JOIN borrow_record_equipment bre ON br.borrow_record_id  = bre.borrow_record_id
            JOIN equipment               e   ON bre.equipment_id     = e.equipment_id
            WHERE br.expected_return_date < CURDATE()
            AND br.borrow_record_id NOT IN (
                SELECT borrow_record_id FROM return_records
            )
            GROUP BY
                br.borrow_record_id,
                br.borrow_date,
                br.expected_return_date,
                b.first_name, b.last_name,
                b.email,
                b.contact_number,
                a.first_name, a.last_name
        ");
    }

    /**
     * Get all borrow_record_ids that are currently overdue.
     * Used in DashboardController to flag records in the grouped display.
     */
    public static function overdueIds(): array
    {
        return static::query()
            ->select('borrow_record_id')
            ->pluck('borrow_record_id')
            ->toArray();
    }

    /**
     * Get the days_overdue value for a specific borrow record.
     */
    public static function daysOverdueFor(int $borrowRecordId): int
    {
        return (int) static::where('borrow_record_id', $borrowRecordId)
            ->value('days_overdue');
    }
}
