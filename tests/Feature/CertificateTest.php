<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\ClassSession;
use App\Models\Lesson;
use App\Models\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->makeRoles();
    }

    private function setupCourse($grade, $attendance)
    {
        $student = $this->makeStudent('سارة');
        $course = $this->makeCourse($this->makeTeacher(), 'Physics');
        $this->enroll($student, $course);

        $lesson = Lesson::create(['course_id' => $course->id, 'title' => 'One', 'type' => 'text', 'body' => 'x']);
        $student->student->completedLessons()->attach($lesson->id);

        $assignment = Assignment::create(['course_id' => $course->id, 'title' => 'HW', 'due_at' => now()->subDay(), 'max_grade' => 20]);
        Submission::create(['assignment_id' => $assignment->id, 'student_id' => $student->student->id, 'file' => 'x', 'original_name' => 'x', 'grade' => $grade]);

        foreach ($attendance as $i => $status) {
            $session = ClassSession::create(['course_id' => $course->id, 'starts_at' => now()->subDays($i + 1)]);
            Attendance::create(['class_session_id' => $session->id, 'student_id' => $student->student->id, 'status' => $status]);
        }

        return [$student, $course];
    }

    public function test_student_who_meets_the_rules_gets_a_certificate()
    {
        [$student, $course] = $this->setupCourse(16, ['present', 'present', 'late', 'absent']);

        $this->actingAs($student)->get('/my/courses/' . $course->id . '/certificate')
            ->assertOk()
            ->assertSee('سارة');

        $certificate = Certificate::first();
        $this->assertEquals(75, $certificate->attendance);
        $this->assertEquals(16, (float) $certificate->grade);
        $this->assertStringStartsWith('EC-', $certificate->code);
    }

    public function test_low_grade_blocks_the_certificate()
    {
        [$student, $course] = $this->setupCourse(10, ['present']);

        $this->actingAs($student)->get('/my/courses/' . $course->id . '/certificate')
            ->assertRedirect('/my/courses/' . $course->id);

        $this->assertEquals(0, Certificate::count());
    }

    public function test_low_attendance_blocks_the_certificate()
    {
        [$student, $course] = $this->setupCourse(18, ['present', 'absent', 'absent']);

        $this->actingAs($student)->get('/my/courses/' . $course->id . '/certificate');

        $this->assertEquals(0, Certificate::count());
    }

    public function test_unfinished_lessons_block_the_certificate()
    {
        [$student, $course] = $this->setupCourse(18, ['present']);
        Lesson::create(['course_id' => $course->id, 'title' => 'Two', 'type' => 'text', 'body' => 'x']);

        $this->actingAs($student)->get('/my/courses/' . $course->id . '/certificate');

        $this->assertEquals(0, Certificate::count());
    }

    public function test_opening_twice_keeps_the_same_certificate()
    {
        [$student, $course] = $this->setupCourse(16, ['present']);

        $this->actingAs($student)->get('/my/courses/' . $course->id . '/certificate');
        $this->actingAs($student)->get('/my/courses/' . $course->id . '/certificate');

        $this->assertEquals(1, Certificate::count());
    }

    public function test_anyone_can_verify_a_certificate()
    {
        [$student, $course] = $this->setupCourse(16, ['present']);
        $this->actingAs($student)->get('/my/courses/' . $course->id . '/certificate');
        $code = Certificate::first()->code;
        auth()->logout();

        $this->get('/verify/' . $code)->assertOk()->assertSee('سارة')->assertSee('Physics');
        $this->get('/verify?code=EC-0000-NOPE')->assertOk()->assertDontSee('سارة');
    }
}
