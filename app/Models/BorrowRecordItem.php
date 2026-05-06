<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowRecordItem extends Model
{
    // 1. Define the custom primary key
    protected $primaryKey = 'borrow_item_id';

    // 2. Allow these fields to be filled during creation
    protected $fillable = [
        'borrow_record_id',
        'equipment_id',
        'quantity',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function borrowRecord()
    {
        return $this->belongsTo(BorrowRecord::class, 'borrow_record_id');
    }
}
