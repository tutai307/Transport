<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = ['plate_number', 'default_volume_m3', 'is_active'];

    protected function casts(): array
    {
        return [
            'default_volume_m3' => 'decimal:2',
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
