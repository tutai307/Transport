<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = ['plate_number', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
