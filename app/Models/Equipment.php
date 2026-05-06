<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'equipment'; // Tells Laravel the table name
    protected $primaryKey = 'equipment_id'; // Tells Laravel the custom ID name

    protected $fillable = [
        'equipment_name',
        'total_quantity',
        'available_quantity'
    ];
}
