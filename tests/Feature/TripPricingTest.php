<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Material;
use App\Models\Project;
use App\Models\Route;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripPricingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Project $project;
    protected Vehicle $vehicle;
    protected Employee $driver;
    protected Employee $otherDriver;
    protected Material $material;
    protected Route $route;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->project = Project::create(['name' => 'Project A', 'is_active' => true]);
        $this->driver = Employee::create(['name' => 'Driver A', 'is_active' => true]);
        $this->otherDriver = Employee::create(['name' => 'Driver B', 'is_active' => true]);
        $this->vehicle = Vehicle::create([
            'plate_number' => '99C-1111',
            'default_driver_id' => $this->driver->id,
            'is_active' => true,
        ]);
        $this->material = Material::create(['name' => 'Cát', 'is_active' => true]);
        $this->route = Route::create([
            'from_location' => 'Point A',
            'to_location' => 'Point B',
            'distance_km' => 10,
            'is_active' => true,
        ]);
    }

    public function test_trip_total_price_is_quantity_times_freight_price(): void
    {
        $this->actingAs($this->user)->post(route('trips.store'), [
            'trip_date' => '2026-07-08',
            'project_id' => $this->project->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'material_id' => $this->material->id,
            'route_id' => $this->route->id,
            'quantity' => 5,
            'freight_price' => 200000,
        ])->assertRedirect();

        $trip = Trip::latest('id')->first();
        $this->assertSame(1000000.0, (float) $trip->total_price);
        $this->assertSame(5.0, (float) $trip->quantity);
    }

    public function test_trip_persists_driver_and_plate_snapshots_on_create(): void
    {
        $this->actingAs($this->user)->post(route('trips.store'), [
            'trip_date' => '2026-07-08',
            'project_id' => $this->project->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'material_id' => $this->material->id,
            'route_id' => $this->route->id,
            'quantity' => 2,
            'freight_price' => 150000,
        ]);

        $trip = Trip::latest('id')->first();
        $this->assertSame('Driver A', $trip->driver_name_snapshot);
        $this->assertSame('99C-1111', $trip->vehicle_plate_snapshot);
    }

    public function test_changing_vehicle_default_driver_does_not_rewrite_history(): void
    {
        $this->actingAs($this->user)->post(route('trips.store'), [
            'trip_date' => '2026-07-08',
            'project_id' => $this->project->id,
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'material_id' => $this->material->id,
            'route_id' => $this->route->id,
            'quantity' => 1,
            'freight_price' => 100000,
        ]);

        $trip = Trip::latest('id')->first();

        // Chuyển tài xế mặc định của xe → chuyến cũ vẫn giữ snapshot Driver A.
        $this->vehicle->update(['default_driver_id' => $this->otherDriver->id]);
        $this->driver->update(['name' => 'Driver A (renamed)']);

        $trip->refresh();
        $this->assertSame('Driver A', $trip->driver_name_snapshot);
    }

    public function test_vehicle_default_driver_endpoint_returns_driver(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('api.vehicle-default-driver', $this->vehicle));

        $response->assertOk()->assertJson([
            'driver_id' => $this->driver->id,
            'driver_name' => 'Driver A',
        ]);
    }

    public function test_trip_validation_requires_core_fields(): void
    {
        $this->actingAs($this->user)->post(route('trips.store'), [])
            ->assertSessionHasErrors([
                'trip_date',
                'project_id',
                'vehicle_id',
                'driver_id',
                'material_id',
                'route_id',
                'quantity',
                'freight_price',
            ]);
    }
}
