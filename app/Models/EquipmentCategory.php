<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentCategory extends Model
{
    protected $primaryKey = 'category_id';
    protected $fillable = ['category_name'];

    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'category_id', 'category_id');
    }
}
