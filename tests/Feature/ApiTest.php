<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->makeRoles();
    }

    public function test_courses_list()
    {
        $teacher = $this->makeTeacher('Mohamed');
        $course = $this->makeCourse($teacher, 'Physics');
        $this->enroll($this->makeStudent(), $course);

        $this->getJson('/api/courses')
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Physics')
            ->assertJsonPath('data.0.teacher', 'Mohamed')
            ->assertJsonPath('data.0.students_count', 1);
    }

    public function test_single_course_with_schedule()
    {
        $course = $this->makeCourse($this->makeTeacher());
        $course->slots()->create(['day' => 0, 'starts_at' => '09:00', 'ends_at' => '10:30', 'room' => 'A']);

        $this->getJson('/api/courses/' . $course->id)
            ->assertOk()
            ->assertJsonPath('data.schedule.0.starts_at', '09:00');
    }

    public function test_missing_course_returns_404()
    {
        $this->getJson('/api/courses/999')->assertNotFound();
    }

    public function test_teacher_list_does_not_leak_account_data()
    {
        $this->makeTeacher('Rana');

        $response = $this->getJson('/api/teachers')->assertOk();

        $this->assertEquals('Rana', $response->json('data.0.name'));
        $this->assertArrayNotHasKey('user_id', $response->json('data.0'));
        $this->assertArrayNotHasKey('email', $response->json('data.0'));
    }
}
