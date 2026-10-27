<?php

namespace Tests\Feature;

use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->makeRoles();
    }

    public function test_guest_is_sent_to_login()
    {
        $this->get('/my')->assertRedirect('/login');
    }

    public function test_student_can_enroll_in_a_course()
    {
        $student = $this->makeStudent();
        $course = $this->makeCourse($this->makeTeacher());

        $this->actingAs($student)->post('/courses/' . $course->id . '/enroll');

        $this->assertDatabaseHas('registrations', [
            'student_id' => $student->student->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_student_cannot_enroll_twice()
    {
        $student = $this->makeStudent();
        $course = $this->makeCourse($this->makeTeacher());

        $this->actingAs($student)->post('/courses/' . $course->id . '/enroll');
        $this->actingAs($student)->post('/courses/' . $course->id . '/enroll')->assertSessionHas('error');

        $this->assertEquals(1, Registration::count());
    }

    public function test_cannot_enroll_in_a_course_without_teacher()
    {
        $student = $this->makeStudent();
        $course = $this->makeCourse();

        $this->actingAs($student)->post('/courses/' . $course->id . '/enroll')->assertSessionHas('error');

        $this->assertEquals(0, Registration::count());
    }

    public function test_student_can_drop_a_course()
    {
        $student = $this->makeStudent();
        $course = $this->makeCourse($this->makeTeacher());
        $this->enroll($student, $course);

        $this->actingAs($student)->delete('/courses/' . $course->id . '/remove-enrollment');

        $this->assertEquals(0, $course->enrolled()->count());
    }

    public function test_login_sends_each_role_to_its_page()
    {
        $student = $this->makeStudent();
        $teacher = $this->makeTeacher();

        $this->post('/login', ['email' => $student->email, 'password' => 'password'])->assertRedirect('/my');
        $this->post('/logout');
        $this->post('/login', ['email' => $teacher->email, 'password' => 'password'])->assertRedirect('/teach');
    }
}
