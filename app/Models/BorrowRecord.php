<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowRecord extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'status_id',
        'borrow_date',
        'expected_return_date',
        'actual_return_date',
        'condition',
        'remarks'
    ];

    // ADD THIS SECTION
    protected $casts = [
        'borrow_date' => 'datetime',
        'expected_return_date' => 'datetime',
        'actual_return_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(BorrowRecordItem::class, 'borrow_record_id');
    }
}
