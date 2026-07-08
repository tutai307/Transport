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

class TripCalendarTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Project $project;
    protected Vehicle $vehicle;
    protected Employee $driver;
    protected Material $material;
    protected Route $route;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->project = Project::create(['name' => 'Project X', 'is_active' => true]);
        $this->driver = Employee::create(['name' => 'Driver X', 'is_active' => true]);
        $this->vehicle = Vehicle::create([
            'plate_number' => '30F-1234',
            'default_driver_id' => $this->driver->id,
            'is_active' => true,
        ]);
        $this->material = Material::create(['name' => 'Đá', 'is_active' => true]);
        $this->route = Route::create([
            'from_location' => 'Kho',
            'to_location' => 'Công trường',
            'is_active' => true,
        ]);
    }

    private function makeTrip(string $date, int $qty, int $freight): Trip
    {
        return Trip::create([
            'trip_date' => $date,
            'project_id' => $this->project->id,
            'vehicle_id' => $this->vehicle->id,
            'vehicle_plate_snapshot' => $this->vehicle->plate_number,
            'driver_id' => $this->driver->id,
            'driver_name_snapshot' => $this->driver->name,
            'material_id' => $this->material->id,
            'route_id' => $this->route->id,
            'quantity' => $qty,
            'freight_price' => $freight,
            'total_price' => $qty * $freight,
        ]);
    }

    public function test_month_calendar_renders_with_day_summary(): void
    {
        $this->makeTrip('2026-07-08', 3, 100000);
        $this->makeTrip('2026-07-08', 2, 100000);

        $response = $this->actingAs($this->user)
            ->get(route('trips.by-month', [
                'project' => $this->project->id,
                'year' => 2026,
                'month' => 7,
            ]));

        $response->assertOk();
        $response->assertSee('Tháng 7/2026');
        $response->assertSee('5 chuyến'); // ngày 8 có tổng qty = 5
    }

    public function test_day_view_lists_trips_and_aggregations(): void
    {
        $this->makeTrip('2026-07-08', 2, 150000); // Driver X, 30F-1234
        $this->makeTrip('2026-07-08', 3, 200000); // cùng xe/tài xế

        $response = $this->actingAs($this->user)
            ->get(route('trips.by-day', [
                'project' => $this->project->id,
                'year' => 2026,
                'month' => 7,
                'day' => 8,
            ]));

        $response->assertOk();
        // Chi tiết
        $response->assertSee('30F-1234');
        $response->assertSee('Driver X');
        // Gom nhóm vật liệu
        $response->assertSee('Đá');
        // Tổng cước = 2*150k + 3*200k = 900k
        $response->assertSee('900.000');
    }

    public function test_day_view_uses_snapshot_after_driver_reassignment(): void
    {
        $trip = $this->makeTrip('2026-07-08', 1, 100000);

        // Đổi tên tài xế + đổi tài xế mặc định của xe.
        $otherDriver = Employee::create(['name' => 'Driver Y', 'is_active' => true]);
        $this->vehicle->update(['default_driver_id' => $otherDriver->id]);
        $this->driver->update(['name' => 'Driver X (renamed)']);

        $response = $this->actingAs($this->user)
            ->get(route('trips.by-day', [
                'project' => $this->project->id,
                'year' => 2026,
                'month' => 7,
                'day' => 8,
            ]));

        $response->assertOk();
        $response->assertSee('Driver X'); // snapshot giữ nguyên
        $response->assertDontSee('Driver X (renamed)');
    }
}
