<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->makeRoles();
    }

    public function test_student_cannot_open_teacher_portal()
    {
        $this->actingAs($this->makeStudent())->get('/teach')->assertForbidden();
    }

    public function test_teacher_can_open_own_course_only()
    {
        $teacher = $this->makeTeacher('أ');
        $other = $this->makeTeacher('ب');
        $mine = $this->makeCourse($teacher);
        $notMine = $this->makeCourse($other);

        $this->actingAs($teacher)->get('/teach/courses/' . $mine->id)->assertOk();
        $this->actingAs($teacher)->get('/teach/courses/' . $notMine->id)->assertForbidden();
        $this->actingAs($teacher)->get('/teach/courses/' . $notMine->id . '/attendance')->assertForbidden();
    }

    public function test_student_cannot_open_course_page_without_enrolling()
    {
        $student = $this->makeStudent();
        $course = $this->makeCourse($this->makeTeacher());

        $this->actingAs($student)->get('/my/courses/' . $course->id)->assertForbidden();

        $this->enroll($student, $course);
        $this->actingAs($student)->get('/my/courses/' . $course->id)->assertOk();
    }

    public function test_admin_can_open_any_course_in_teacher_portal()
    {
        $admin = $this->makeUser(1);
        $course = $this->makeCourse($this->makeTeacher());

        $this->actingAs($admin)->get('/teach/courses/' . $course->id)->assertOk();
    }
}
