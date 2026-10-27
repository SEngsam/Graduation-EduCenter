<?php

namespace Tests\Feature;

use App\Models\CourseQuestion;
use App\Models\QuestionReply;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscussionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->makeRoles();
    }

    public function test_enrolled_student_can_ask_and_teacher_gets_notified()
    {
        $teacher = $this->makeTeacher();
        $student = $this->makeStudent('سارة');
        $course = $this->makeCourse($teacher);
        $this->enroll($student, $course);

        $this->actingAs($student)->post('/courses/' . $course->id . '/discussion', ['title' => 'شو يعني تسارع؟'])
            ->assertRedirect();

        $this->assertEquals(1, CourseQuestion::count());
        $this->assertEquals('question', $teacher->notifications()->first()->data['type']);

        $this->actingAs($student)->get('/courses/' . $course->id . '/discussion')
            ->assertOk()
            ->assertSee('شو يعني تسارع؟');
    }

    public function test_student_not_in_the_course_cannot_see_or_ask()
    {
        $course = $this->makeCourse($this->makeTeacher());
        $other = $this->makeStudent();

        $this->actingAs($other)->get('/courses/' . $course->id . '/discussion')->assertForbidden();
        $this->actingAs($other)->post('/courses/' . $course->id . '/discussion', ['title' => 'x'])->assertForbidden();
        $this->assertEquals(0, CourseQuestion::count());
    }

    public function test_teacher_reply_notifies_the_student()
    {
        $teacher = $this->makeTeacher();
        $student = $this->makeStudent();
        $course = $this->makeCourse($teacher);
        $this->enroll($student, $course);

        $question = CourseQuestion::create(['course_id' => $course->id, 'user_id' => $student->id, 'title' => 'سؤال']);

        $this->actingAs($teacher)->post('/questions/' . $question->id . '/replies', ['body' => 'جواب'])
            ->assertRedirect();

        $this->assertEquals(1, QuestionReply::count());
        $this->assertEquals('reply', $student->notifications()->first()->data['type']);
        $this->assertTrue($question->fresh()->answeredByTeacher());

        $this->actingAs($teacher)->get('/courses/' . $course->id . '/discussion')
            ->assertOk()
            ->assertSee('جواب');
    }

    public function test_other_teacher_cannot_reply()
    {
        $course = $this->makeCourse($this->makeTeacher());
        $student = $this->makeStudent();
        $this->enroll($student, $course);
        $question = CourseQuestion::create(['course_id' => $course->id, 'user_id' => $student->id, 'title' => 'سؤال']);

        $this->actingAs($this->makeTeacher('غريب'))->post('/questions/' . $question->id . '/replies', ['body' => 'x'])
            ->assertForbidden();
    }

    public function test_teacher_can_delete_but_other_student_cannot()
    {
        $teacher = $this->makeTeacher();
        $course = $this->makeCourse($teacher);
        $asker = $this->makeStudent();
        $classmate = $this->makeStudent();
        $this->enroll($asker, $course);
        $this->enroll($classmate, $course);

        $question = CourseQuestion::create(['course_id' => $course->id, 'user_id' => $asker->id, 'title' => 'سؤال']);
        $reply = QuestionReply::create(['course_question_id' => $question->id, 'user_id' => $asker->id, 'body' => 'رد']);

        $this->actingAs($classmate)->delete('/replies/' . $reply->id)->assertForbidden();
        $this->actingAs($classmate)->delete('/questions/' . $question->id)->assertForbidden();

        $this->actingAs($teacher)->delete('/questions/' . $question->id)->assertRedirect();
        $this->assertEquals(0, CourseQuestion::count());
        $this->assertEquals(0, QuestionReply::count());
    }
}
