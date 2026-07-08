<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $fillable = [
        'trip_date',
        'project_id',
        'vehicle_id',
        'vehicle_plate_snapshot',
        'driver_id',
        'driver_name_snapshot',
        'material_id',
        'route_id',
        'quantity',
        'freight_price',
        'total_price',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'trip_date' => 'date',
            'quantity' => 'decimal:2',
            'freight_price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
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
}
