<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'admin_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Full name accessor — use $admin->full_name anywhere
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'admin_id', 'admin_id');
    }

    public function borrowRecords()
    {
        return $this->hasMany(BorrowRecord::class, 'admin_id', 'admin_id');
    }

    public function returnRecords()
    {
        return $this->hasMany(ReturnRecord::class, 'admin_id', 'admin_id');
    }
}
