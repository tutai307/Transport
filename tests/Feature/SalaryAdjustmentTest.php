<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use App\Models\Project;
use App\Models\SalaryAdjustment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalaryAdjustmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Employee $driver;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        
        $this->driver = Employee::create([
            'name' => 'Driver A',
            'phone' => '0987654321',
            'is_active' => true,
        ]);

        $this->project = Project::create([
            'name' => 'Project A',
            'description' => 'Test project',
            'is_active' => true,
        ]);
    }

    public function test_user_can_create_salary_adjustment(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('salary-adjustments.store'), [
                'trip_date' => '2026-06-01',
                'driver_id' => $this->driver->id,
                'project_id' => $this->project->id,
                'type' => 'addition',
                'amount' => '500.000', // Test currency formatting cleanup
                'note' => 'Phụ cấp đổ dầu',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('salary_adjustments', [
            'driver_id' => $this->driver->id,
            'project_id' => $this->project->id,
            'type' => 'addition',
            'amount' => 500000,
            'note' => 'Phụ cấp đổ dầu',
        ]);
    }

    public function test_user_can_delete_salary_adjustment(): void
    {
        $adjustment = SalaryAdjustment::create([
            'trip_date' => '2026-06-01',
            'driver_id' => $this->driver->id,
            'project_id' => $this->project->id,
            'type' => 'deduction',
            'amount' => 200000,
            'note' => 'Ứng lương',
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('salary-adjustments.destroy', $adjustment));

        $response->assertRedirect();
        $this->assertDatabaseMissing('salary_adjustments', [
            'id' => $adjustment->id,
        ]);
    }

    public function test_payroll_shows_driver_with_only_salary_adjustments(): void
    {
        // Create a driver adjustment with NO trips in 2026-06
        $adjustment = SalaryAdjustment::create([
            'trip_date' => '2026-06-15',
            'driver_id' => $this->driver->id,
            'project_id' => $this->project->id,
            'type' => 'addition',
            'amount' => 150000,
            'note' => 'Phụ cấp giữa ca',
        ]);

        // 1. Visit index page
        $response = $this->actingAs($this->user)->get(route('payroll.index'));
        $response->assertStatus(200);
        $response->assertSee(number_format(150000, 0, ',', '.'));

        // 2. Visit driver page
        $response = $this->actingAs($this->user)->get(route('payroll.by-driver', $this->driver));
        $response->assertStatus(200);
        $response->assertSee('2026');

        // 3. Visit by year page
        $response = $this->actingAs($this->user)->get(route('payroll.by-year', [$this->driver, 2026]));
        $response->assertStatus(200);
        $response->assertSee('Tháng 6');

        // 4. Visit by month page
        $response = $this->actingAs($this->user)->get(route('payroll.by-month', [$this->driver, 2026, 6]));
        $response->assertStatus(200);
        $response->assertSee('Phụ cấp giữa ca');

        // 5. Test export
        $response = $this->actingAs($this->user)->get(route('payroll.export', $this->driver) . '?date_from=2026-06-01&date_to=2026-06-30');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
