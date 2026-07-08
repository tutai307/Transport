<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = ['plate_number', 'default_driver_id', 'is_active'];

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

    public function defaultDriver()
    {
        return $this->belongsTo(Employee::class, 'default_driver_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
