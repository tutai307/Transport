<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouteMaterial extends Model
{
    protected $table = 'route_material';

    protected $fillable = ['route_id', 'material_id', 'price_per_m3'];

    protected function casts(): array
    {
        return [
            'price_per_m3' => 'decimal:2',
        ];
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
