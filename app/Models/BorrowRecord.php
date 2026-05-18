<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowRecord extends Model
{
    protected $primaryKey = 'borrow_record_id';

    protected $fillable = [
        'borrower_id',
        'admin_id',
        'status_id',
        'borrow_date',
        'check_out_time',
        'expected_return_date',
    ];

    protected function casts(): array
    {
        return [
            'borrow_date'           => 'date',
            'expected_return_date'  => 'date',
            'check_out_time'        => 'string',
        ];
    }

    public function borrower()
    {
        return $this->belongsTo(Borrower::class, 'borrower_id', 'borrower_id')->withTrashed();
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'status_id');
    }

    // Line items (equipment borrowed)
    public function items()
    {
        return $this->hasMany(BorrowRecordEquipment::class, 'borrow_record_id', 'borrow_record_id');
    }

    // The return record (if it exists)
    public function returnRecord()
    {
        return $this->hasOne(ReturnRecord::class, 'borrow_record_id', 'borrow_record_id');
    }

    // Convenience: is this record still active (no return yet)?
    public function getIsActiveAttribute(): bool
    {
        return is_null($this->returnRecord);
    }

    // Convenience: is overdue?
    public function getIsOverdueAttribute(): bool
    {
        return $this->is_active && $this->expected_return_date->isPast();
    }
}
