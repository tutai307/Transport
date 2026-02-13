<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Vehicle;
use App\Models\Employee;
use App\Models\Material;
use App\Models\Route;
use App\Models\Trip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripPricingTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $project;
    protected $vehicle;
    protected $driver;
    protected $material;
    protected $route;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->project = Project::create(['name' => 'Project A', 'is_active' => true]);
        $this->vehicle = Vehicle::create(['plate_number' => '99C-1111', 'default_volume_m3' => 10, 'is_active' => true]);
        $this->driver = Employee::create(['name' => 'Driver A', 'is_active' => true]);
        $this->material = Material::create([
            'name' => 'Material A',
            'unit' => 'm3',
            'import_price' => 100000,
            'sell_price' => 150000,
            'is_active' => true
        ]);
        $this->route = Route::create([
            'from_location' => 'Point A',
            'to_location' => 'Point B',
            'distance_km' => 10,
            'is_active' => true
        ]);
    }

    public function test_trip_can_be_created_with_manual_price()
    {
        $response = $this->actingAs($this->user)->post(route('trips.store'), [
            'trip_date' => '2026-02-12',
            'project_id' => $this->project->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'material_id' => $this->material->id,
            'route_id' => $this->route->id,
            'volume_m3' => 10,
            'price_per_m3' => 200000, // Manual price different from material sell price
            'note' => 'Test trip',
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('trips', [
            'project_id' => $this->project->id,
            'volume_m3' => 10,
            'price_per_m3' => 200000,
            'total_price' => 2000000, // 10 * 200,000
            'cost_per_m3' => 100000, // Material import price
            'profit' => 1000000, // (200,000 - 100,000) * 10
        ]);
    }

    public function test_trip_total_price_and_profit_calculated_correctly()
    {
        // Data for calculation
        $volume = 12.5;
        $price = 180000;
        $importPrice = $this->material->import_price;

        $response = $this->actingAs($this->user)->post(route('trips.store'), [
            'trip_date' => '2026-02-12',
            'project_id' => $this->project->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'material_id' => $this->material->id,
            'route_id' => $this->route->id,
            'volume_m3' => $volume,
            'price_per_m3' => $price,
            'note' => 'Calculation test',
        ]);

        $expectedTotal = $volume * $price;
        $expectedProfit = ($price - $importPrice) * $volume;

        $trip = Trip::latest()->first();
        
        $this->assertEquals($expectedTotal, $trip->total_price);
        $this->assertEquals($expectedProfit, $trip->profit);
    }

    public function test_trip_validation_requires_fields()
    {
        $response = $this->actingAs($this->user)->post(route('trips.store'), []);

        $response->assertSessionHasErrors([
            'trip_date',
            'project_id',
            'vehicle_id',
            'driver_id',
            'material_id',
            'route_id',
            'volume_m3',
            'price_per_m3',
        ]);
    }
}
