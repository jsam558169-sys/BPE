<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $primaryKey = 'equipment_id';
    protected $table = 'equipment';

    protected $fillable = [
        'category_id',
        'admin_id',
        'equipment_name',
        'total_quantity',
        'available_quantity',
    ];

    public function category()
    {
        return $this->belongsTo(EquipmentCategory::class, 'category_id', 'category_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }

    public function borrowRecordItems()
    {
        return $this->hasMany(BorrowRecordEquipment::class, 'equipment_id', 'equipment_id');
    }

    // Helper: is there stock available?
    public function getIsAvailableAttribute(): bool
    {
        return $this->available_quantity > 0;
    }

    // Helper: availability label for the inventory view
    public function getAvailabilityStatusAttribute(): string
    {
        if ($this->available_quantity === 0) return 'Out of Stock';
        if ($this->available_quantity < ($this->total_quantity * 0.25)) return 'Low Stock';
        return 'Available';
    }
}
