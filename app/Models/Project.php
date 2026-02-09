<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'description', 'is_active'];

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
