<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Role;
use Database\Seeders\AbilityRoleSeeder;
use Database\Seeders\RolesDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolesDataSeeder::class, AbilityRoleSeeder::class]);
    }

    private function courseWithAttendance()
    {
        $course = $this->makeCourse($this->makeTeacher(), 'Physics');
        $students = [$this->makeStudent(), $this->makeStudent(), $this->makeStudent(), $this->makeStudent()];
        $session = ClassSession::create(['course_id' => $course->id, 'starts_at' => now()->subDays(3)]);

        foreach (['present', 'late', 'absent', 'present'] as $i => $status) {
            $this->enroll($students[$i], $course);
            Attendance::create(['class_session_id' => $session->id, 'student_id' => $students[$i]->student->id, 'status' => $status]);
        }

        return $course;
    }

    public function test_only_people_with_the_permission_see_reports()
    {
        $this->actingAs($this->makeStudent())->get(route('admin.reports.index'))->assertRedirect('not_allowed');
        $this->actingAs($this->makeUser(Role::ROLE_ADMIN))->get(route('admin.reports.index'))->assertOk();
    }

    public function test_attendance_rate_counts_late_as_present()
    {
        $this->courseWithAttendance();

        $response = $this->actingAs($this->makeUser(Role::ROLE_ADMIN))->get(route('admin.reports.index'));

        $this->assertEquals(75, $response->viewData('stats')['attendance']);
        $this->assertEquals(4, $response->viewData('stats')['enrollments']);
    }

    public function test_export_gives_a_csv_with_each_course()
    {
        $this->courseWithAttendance();

        $response = $this->actingAs($this->makeUser(Role::ROLE_ADMIN))->get(route('admin.reports.export'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('"Physics"', $response->getContent());
        $this->assertStringContainsString('"75%"', $response->getContent());
    }

    public function test_unknown_period_falls_back_to_six_months()
    {
        $response = $this->actingAs($this->makeUser(Role::ROLE_ADMIN))->get(route('admin.reports.index', ['months' => 99]));

        $this->assertEquals(6, $response->viewData('months'));
        $this->assertCount(6, $response->viewData('enrollments'));
    }
}
