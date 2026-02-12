<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Report;
use App\Models\User;
use App\Models\UserAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_report_dashboard()
    {
        $user = User::factory()->create();
        $department = Department::create(['name' => 'Department A', 'code' => 'DEPA', 'type' => 'Mục vụ']);
        
        // Assign user to department
        UserAssignment::create([
            'user_id' => $user->id,
            'department_id' => $department->id,
            'role' => 'member',
            'permissions' => ['reports' => true], // Grant permission
            'status' => 'active'
        ]);

        $this->actingAs($user)
            ->get(route('reports.index'))
            ->assertStatus(200);
    }

    public function test_user_cannot_view_other_department_reports()
    {
        // User A in Dept A
        $userA = User::factory()->create();
        $deptA = Department::create(['name' => 'Dept A', 'code' => 'DA', 'type' => 'Mục vụ']);
        UserAssignment::create([
            'user_id' => $userA->id,
            'department_id' => $deptA->id,
            'role' => 'member',
            'permissions' => ['reports' => true],
            'status' => 'active'
        ]);

        // Report in Dept B
        $deptB = Department::create(['name' => 'Dept B', 'code' => 'DB', 'type' => 'Mục vụ']);
        $reportB = Report::create([
            'department_id' => $deptB->id,
            'user_id' => User::factory()->create()->id,
            'type' => 'ChuaNhat',
            'reporting_date' => now(),
            'attendance_count' => 10,
            'status' => 'published'
        ]);

        // User A tries to view Report B
        // Note: Livewire component handles filtering, but we can test Policy directly too
        $this->actingAs($userA);
        $this->assertFalse($userA->can('view', $reportB));
    }

    public function test_create_report_saves_data()
    {
        $user = User::factory()->create();
        $department = Department::create(['name' => 'Department A', 'code' => 'DEPA', 'type' => 'Mục vụ']);
        UserAssignment::create([
            'user_id' => $user->id,
            'department_id' => $department->id,
            'role' => 'member',
            'permissions' => ['reports' => true],
            'status' => 'active'
        ]);

        $this->actingAs($user);

        Livewire::test('report.create-report-form')
            ->set('department_id', $department->id)
            ->set('reporting_date', '2023-10-27')
            ->set('attendance_count', 50)
            ->set('topic', 'Test Topic')
            ->call('save')
            ->assertDispatched('reportCreated');

        $this->assertDatabaseHas('reports', [
            'department_id' => $department->id,
            'attendance_count' => 50,
        ]);
        
        $report = Report::first();
        $this->assertEquals('Test Topic', $report->content['topic']);
    }
}
