<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryAdjustment extends Model
{
    protected $fillable = [
        'trip_date',
        'driver_id',
        'project_id',
        'amount',
        'type',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'trip_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
