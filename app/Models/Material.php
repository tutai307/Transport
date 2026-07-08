<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['name', 'code', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($material) {
            if (empty($material->code)) {
                // Generates a slug-like code, e.g., "Cat den" -> "CAT-DEN"
                $material->code = strtoupper(\Illuminate\Support\Str::slug($material->name));
                // Ensure uniqueness if needed, but for now relying on DB constraint to fail if duplicate
            }
        });
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
