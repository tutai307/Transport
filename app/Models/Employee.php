<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['name', 'phone', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function trips()
    {
        return $this->hasMany(Trip::class, 'driver_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
