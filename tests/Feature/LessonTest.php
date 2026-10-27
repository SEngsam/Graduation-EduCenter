<?php

namespace Tests\Feature;

use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->makeRoles();
    }

    public function test_teacher_can_add_a_lesson()
    {
        $teacher = $this->makeTeacher();
        $course = $this->makeCourse($teacher);

        $this->actingAs($teacher)->post('/teach/courses/' . $course->id . '/lessons', [
            'title' => 'Newton laws',
            'type' => 'link',
            'url' => 'https://example.com',
            'is_published' => 1,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('lessons', ['course_id' => $course->id, 'title' => 'Newton laws', 'is_published' => 1]);
    }

    public function test_video_lesson_needs_a_link()
    {
        $teacher = $this->makeTeacher();
        $course = $this->makeCourse($teacher);

        $this->actingAs($teacher)->post('/teach/courses/' . $course->id . '/lessons', [
            'title' => 'No link',
            'type' => 'video',
        ])->assertSessionHasErrors('url');
    }

    public function test_draft_lessons_are_hidden_from_students()
    {
        $teacher = $this->makeTeacher();
        $student = $this->makeStudent();
        $course = $this->makeCourse($teacher);
        $this->enroll($student, $course);

        Lesson::create(['course_id' => $course->id, 'title' => 'Visible lesson', 'type' => 'text', 'body' => 'x', 'is_published' => true]);
        Lesson::create(['course_id' => $course->id, 'title' => 'Hidden lesson', 'type' => 'text', 'body' => 'x', 'is_published' => false]);

        $this->actingAs($student)->get('/my/courses/' . $course->id)
            ->assertSee('Visible lesson')
            ->assertDontSee('Hidden lesson');
    }

    public function test_marking_lessons_done_updates_progress()
    {
        $teacher = $this->makeTeacher();
        $student = $this->makeStudent();
        $course = $this->makeCourse($teacher);
        $this->enroll($student, $course);

        $first = Lesson::create(['course_id' => $course->id, 'title' => 'One', 'type' => 'text', 'body' => 'x']);
        Lesson::create(['course_id' => $course->id, 'title' => 'Two', 'type' => 'text', 'body' => 'x']);

        $this->actingAs($student)->post('/my/lessons/' . $first->id . '/done');

        $response = $this->actingAs($student)->get('/my/courses/' . $course->id);
        $this->assertEquals(50, $response->viewData('progress'));

        $this->actingAs($student)->post('/my/lessons/' . $first->id . '/done');
        $response = $this->actingAs($student)->get('/my/courses/' . $course->id);
        $this->assertEquals(0, $response->viewData('progress'));
    }
}
