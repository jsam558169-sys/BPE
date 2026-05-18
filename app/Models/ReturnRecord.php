<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnRecord extends Model
{
    protected $primaryKey = 'return_id';

    protected $fillable = [
        'borrow_record_id',
        'admin_id',
        'return_date',
        'return_time',
        'remarks',
        'condition'
    ];

    protected function casts(): array
    {
        return [
            'return_date' => 'date',
            'return_time' => 'string',
        ];
    }

    public function borrowRecord()
    {
        return $this->belongsTo(BorrowRecord::class, 'borrow_record_id', 'borrow_record_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }
}
