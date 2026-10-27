<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\ClassSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->makeRoles();
    }

    public function test_teacher_can_take_attendance()
    {
        $teacher = $this->makeTeacher();
        $course = $this->makeCourse($teacher);
        $sara = $this->makeStudent('سارة');
        $omar = $this->makeStudent('عمر');
        $this->enroll($sara, $course);
        $this->enroll($omar, $course);

        $this->actingAs($teacher)->post('/teach/courses/' . $course->id . '/attendance', [
            'date' => '2026-10-20',
            'time' => '09:00',
            'status' => [
                $sara->student->id => 'present',
                $omar->student->id => 'absent',
            ],
            'note' => [$omar->student->id => 'مريض'],
        ])->assertSessionHas('success');

        $this->assertEquals(1, ClassSession::count());
        $this->assertDatabaseHas('attendances', ['student_id' => $omar->student->id, 'status' => 'absent', 'note' => 'مريض']);
    }

    public function test_saving_again_updates_instead_of_duplicating()
    {
        $teacher = $this->makeTeacher();
        $course = $this->makeCourse($teacher);
        $sara = $this->makeStudent();
        $this->enroll($sara, $course);

        $data = ['date' => '2026-10-20', 'time' => '09:00', 'status' => [$sara->student->id => 'present']];
        $this->actingAs($teacher)->post('/teach/courses/' . $course->id . '/attendance', $data);

        $data['status'][$sara->student->id] = 'late';
        $this->actingAs($teacher)->post('/teach/courses/' . $course->id . '/attendance', $data);

        $this->assertEquals(1, Attendance::count());
        $this->assertEquals('late', Attendance::first()->status);
    }

    public function test_students_from_other_courses_are_ignored()
    {
        $teacher = $this->makeTeacher();
        $course = $this->makeCourse($teacher);
        $stranger = $this->makeStudent();

        $this->actingAs($teacher)->post('/teach/courses/' . $course->id . '/attendance', [
            'date' => '2026-10-20',
            'time' => '09:00',
            'status' => [$stranger->student->id => 'present'],
        ]);

        $this->assertEquals(0, Attendance::count());
    }

    public function test_wrong_status_is_rejected()
    {
        $teacher = $this->makeTeacher();
        $course = $this->makeCourse($teacher);
        $sara = $this->makeStudent();
        $this->enroll($sara, $course);

        $this->actingAs($teacher)->post('/teach/courses/' . $course->id . '/attendance', [
            'date' => '2026-10-20',
            'time' => '09:00',
            'status' => [$sara->student->id => 'sleeping'],
        ])->assertSessionHasErrors();

        $this->assertEquals(0, Attendance::count());
    }
}
