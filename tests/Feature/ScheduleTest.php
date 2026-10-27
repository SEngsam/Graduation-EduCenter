<?php

namespace Tests\Feature;

use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->makeRoles();
    }

    public function test_student_cannot_enroll_in_a_clashing_course()
    {
        $student = $this->makeStudent();
        $physics = $this->makeCourse($this->makeTeacher(), 'Physics');
        $art = $this->makeCourse($this->makeTeacher(), 'Art');
        $physics->slots()->create(['day' => 0, 'starts_at' => '09:00', 'ends_at' => '10:30']);
        $art->slots()->create(['day' => 0, 'starts_at' => '10:00', 'ends_at' => '11:00']);
        $this->enroll($student, $physics);

        $this->actingAs($student)->post('/courses/' . $art->id . '/enroll')->assertSessionHas('error');

        $this->assertFalse(Registration::where('course_id', $art->id)->exists());
    }

    public function test_back_to_back_classes_are_fine()
    {
        $student = $this->makeStudent();
        $physics = $this->makeCourse($this->makeTeacher(), 'Physics');
        $art = $this->makeCourse($this->makeTeacher(), 'Art');
        $physics->slots()->create(['day' => 0, 'starts_at' => '09:00', 'ends_at' => '10:30']);
        $art->slots()->create(['day' => 0, 'starts_at' => '10:30', 'ends_at' => '12:00']);
        $this->enroll($student, $physics);

        $this->actingAs($student)->post('/courses/' . $art->id . '/enroll')->assertSessionHas('success');
    }

    public function test_same_time_on_another_day_is_fine()
    {
        $student = $this->makeStudent();
        $physics = $this->makeCourse($this->makeTeacher(), 'Physics');
        $art = $this->makeCourse($this->makeTeacher(), 'Art');
        $physics->slots()->create(['day' => 0, 'starts_at' => '09:00', 'ends_at' => '10:30']);
        $art->slots()->create(['day' => 2, 'starts_at' => '09:00', 'ends_at' => '10:30']);
        $this->enroll($student, $physics);

        $this->actingAs($student)->post('/courses/' . $art->id . '/enroll')->assertSessionHas('success');
    }

    public function test_teacher_cannot_book_two_classes_at_the_same_time()
    {
        $teacher = $this->makeTeacher();
        $physics = $this->makeCourse($teacher, 'Physics');
        $lab = $this->makeCourse($teacher, 'Lab');
        $physics->slots()->create(['day' => 1, 'starts_at' => '13:00', 'ends_at' => '14:30']);

        $this->actingAs($teacher)->post('/teach/courses/' . $lab->id . '/slots', [
            'day' => 1, 'starts_at' => '14:00', 'ends_at' => '15:00',
        ])->assertSessionHasErrors('starts_at');

        $this->assertEquals(0, $lab->slots()->count());
    }

    public function test_schedule_shows_only_my_courses()
    {
        $student = $this->makeStudent();
        $mine = $this->makeCourse($this->makeTeacher(), 'Physics');
        $other = $this->makeCourse($this->makeTeacher(), 'Chemistry');
        $mine->slots()->create(['day' => 0, 'starts_at' => '09:00', 'ends_at' => '10:30', 'room' => 'Room A']);
        $other->slots()->create(['day' => 0, 'starts_at' => '12:00', 'ends_at' => '13:00']);
        $this->enroll($student, $mine);

        $this->actingAs($student)->get('/schedule')
            ->assertOk()
            ->assertSee('Physics')
            ->assertSee('Room A')
            ->assertDontSee('Chemistry');
    }

    public function test_teacher_schedule_shows_teaching_courses()
    {
        $teacher = $this->makeTeacher();
        $course = $this->makeCourse($teacher, 'Physics');
        $course->slots()->create(['day' => 3, 'starts_at' => '09:00', 'ends_at' => '10:00']);

        $this->actingAs($teacher)->get('/schedule')->assertOk()->assertSee('Physics');
    }
}
