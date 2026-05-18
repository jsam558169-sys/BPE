<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowRecordEquipment extends Model
{
    protected $primaryKey = 'borrow_record_equipment_id';
    protected $table = 'borrow_record_equipment';

    protected $fillable = [
        'borrow_record_id',
        'equipment_id',
        'quantity_borrowed',
    ];

    public function borrowRecord()
    {
        return $this->belongsTo(BorrowRecord::class, 'borrow_record_id', 'borrow_record_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'equipment_id');
    }
}
