<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = ['from_location', 'to_location', 'distance_km', 'is_active'];

    protected function casts(): array
    {
        return [
            'distance_km' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function routeMaterials()
    {
        return $this->hasMany(RouteMaterial::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper: hiển thị tên tuyến đường
    public function getFullNameAttribute(): string
    {
        return $this->from_location . ' → ' . $this->to_location;
    }
}
