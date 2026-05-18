<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Borrower extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $primaryKey = 'borrower_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'contact_number',
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

    public function getFullNameAttribute(): string
    {
        $middle = $this->middle_name ? " {$this->middle_name}" : '';
        return "{$this->first_name}{$middle} {$this->last_name}";
    }

    public function borrowRecords()
    {
        return $this->hasMany(BorrowRecord::class, 'borrower_id', 'borrower_id');
    }
}
