<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->makeRoles();
        Storage::fake('local');
    }

    private function makeAssignment($course, $days = 3)
    {
        return Assignment::create([
            'course_id' => $course->id,
            'title' => 'Pendulum report',
            'due_at' => now()->addDays($days),
            'max_grade' => 20,
        ]);
    }

    public function test_teacher_can_create_an_assignment()
    {
        $teacher = $this->makeTeacher();
        $course = $this->makeCourse($teacher);

        $this->actingAs($teacher)->post('/teach/courses/' . $course->id . '/assignments', [
            'title' => 'Homework 1',
            'due_at' => '2026-12-01 23:59',
            'max_grade' => 10,
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('assignments', ['course_id' => $course->id, 'title' => 'Homework 1', 'max_grade' => 10]);
    }

    public function test_student_can_hand_in_a_file()
    {
        $teacher = $this->makeTeacher();
        $student = $this->makeStudent();
        $course = $this->makeCourse($teacher);
        $this->enroll($student, $course);
        $assignment = $this->makeAssignment($course);

        $this->actingAs($student)->post('/my/assignments/' . $assignment->id . '/submit', [
            'file' => UploadedFile::fake()->create('report.pdf', 100, 'application/pdf'),
        ])->assertSessionHas('success');

        $submission = Submission::first();
        $this->assertEquals('report.pdf', $submission->original_name);
        Storage::disk('local')->assertExists($submission->file);
    }

    public function test_handing_in_again_replaces_the_old_file()
    {
        $teacher = $this->makeTeacher();
        $student = $this->makeStudent();
        $course = $this->makeCourse($teacher);
        $this->enroll($student, $course);
        $assignment = $this->makeAssignment($course);

        $this->actingAs($student)->post('/my/assignments/' . $assignment->id . '/submit', ['file' => UploadedFile::fake()->create('v1.pdf', 50)]);
        $oldFile = Submission::first()->file;
        $this->actingAs($student)->post('/my/assignments/' . $assignment->id . '/submit', ['file' => UploadedFile::fake()->create('v2.pdf', 50)]);

        $this->assertEquals(1, Submission::count());
        $this->assertEquals('v2.pdf', Submission::first()->original_name);
        Storage::disk('local')->assertMissing($oldFile);
    }

    public function test_student_outside_the_course_cannot_hand_in()
    {
        $course = $this->makeCourse($this->makeTeacher());
        $assignment = $this->makeAssignment($course);

        $this->actingAs($this->makeStudent())->post('/my/assignments/' . $assignment->id . '/submit', [
            'file' => UploadedFile::fake()->create('report.pdf', 50),
        ])->assertForbidden();
    }

    public function test_teacher_can_grade_and_student_sees_it()
    {
        $teacher = $this->makeTeacher();
        $student = $this->makeStudent();
        $course = $this->makeCourse($teacher);
        $this->enroll($student, $course);
        $assignment = $this->makeAssignment($course);
        $this->actingAs($student)->post('/my/assignments/' . $assignment->id . '/submit', ['file' => UploadedFile::fake()->create('r.pdf', 50)]);
        $submission = Submission::first();

        $this->actingAs($teacher)->put('/teach/submissions/' . $submission->id, [
            'grade' => 17,
            'feedback' => 'Good work',
        ])->assertSessionHas('success');

        $this->assertEquals(17, $submission->fresh()->grade);
        $this->actingAs($student)->get('/my/courses/' . $course->id)->assertSee('Good work');
    }

    public function test_grade_cannot_go_above_the_max()
    {
        $teacher = $this->makeTeacher();
        $student = $this->makeStudent();
        $course = $this->makeCourse($teacher);
        $this->enroll($student, $course);
        $assignment = $this->makeAssignment($course);
        $this->actingAs($student)->post('/my/assignments/' . $assignment->id . '/submit', ['file' => UploadedFile::fake()->create('r.pdf', 50)]);

        $this->actingAs($teacher)->put('/teach/submissions/' . Submission::first()->id, ['grade' => 25])
            ->assertSessionHasErrors('grade');
    }

    public function test_graded_work_cannot_be_replaced()
    {
        $teacher = $this->makeTeacher();
        $student = $this->makeStudent();
        $course = $this->makeCourse($teacher);
        $this->enroll($student, $course);
        $assignment = $this->makeAssignment($course);
        $this->actingAs($student)->post('/my/assignments/' . $assignment->id . '/submit', ['file' => UploadedFile::fake()->create('first.pdf', 50)]);
        $this->actingAs($teacher)->put('/teach/submissions/' . Submission::first()->id, ['grade' => 15]);

        $this->actingAs($student)->post('/my/assignments/' . $assignment->id . '/submit', ['file' => UploadedFile::fake()->create('second.pdf', 50)])
            ->assertSessionHas('error');

        $this->assertEquals('first.pdf', Submission::first()->original_name);
    }

    public function test_other_teacher_cannot_grade()
    {
        $teacher = $this->makeTeacher();
        $student = $this->makeStudent();
        $course = $this->makeCourse($teacher);
        $this->enroll($student, $course);
        $assignment = $this->makeAssignment($course);
        $this->actingAs($student)->post('/my/assignments/' . $assignment->id . '/submit', ['file' => UploadedFile::fake()->create('r.pdf', 50)]);

        $this->actingAs($this->makeTeacher('غريب'))->put('/teach/submissions/' . Submission::first()->id, ['grade' => 20])
            ->assertForbidden();

        $this->assertNull(Submission::first()->grade);
    }
}
