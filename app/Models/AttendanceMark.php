<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceMark extends Model
{
    protected $fillable = [
        'attendance_date', 'project_id', 'vehicle_id', 'driver_id',
        'material_id', 'route_id', 'freight_price', 'marked_at',
        'is_committed', 'trip_id',
    ];

    protected function casts(): array
    {
        return [
            'marked_at'     => 'datetime',
            'is_committed'  => 'boolean',
            'freight_price' => 'decimal:2',
        ];
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
}
