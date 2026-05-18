<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $primaryKey = 'status_id';
    protected $fillable = ['status_name'];

    public function borrowRecords()
    {
        return $this->hasMany(BorrowRecord::class, 'status_id', 'status_id');
    }
}
