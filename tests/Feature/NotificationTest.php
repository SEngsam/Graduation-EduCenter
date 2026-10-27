<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Submission;
use App\Notifications\CourseNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->makeRoles();
        Storage::fake('local');
    }

    public function test_new_assignment_notifies_every_student_in_the_course()
    {
        Notification::fake();
        $teacher = $this->makeTeacher();
        $course = $this->makeCourse($teacher);
        $sara = $this->makeStudent();
        $omar = $this->makeStudent();
        $outsider = $this->makeStudent();
        $this->enroll($sara, $course);
        $this->enroll($omar, $course);

        $this->actingAs($teacher)->post('/teach/courses/' . $course->id . '/assignments', [
            'title' => 'Homework', 'due_at' => '2026-12-01 23:59', 'max_grade' => 20,
        ]);

        Notification::assertSentTo([$sara, $omar], CourseNotification::class, function ($n) {
            return $n->type == 'assignment';
        });
        Notification::assertNotSentTo($outsider, CourseNotification::class);
    }

    public function test_grading_notifies_the_student_and_email_follows_settings()
    {
        Notification::fake();
        $teacher = $this->makeTeacher();
        $student = $this->makeStudent();
        $course = $this->makeCourse($teacher);
        $this->enroll($student, $course);
        $assignment = Assignment::create(['course_id' => $course->id, 'title' => 'HW', 'due_at' => now()->addDay(), 'max_grade' => 20]);
        $submission = Submission::create(['assignment_id' => $assignment->id, 'student_id' => $student->student->id, 'file' => 'x.pdf', 'original_name' => 'x.pdf']);

        $this->actingAs($teacher)->put('/teach/submissions/' . $submission->id, ['grade' => 18]);

        Notification::assertSentTo($student, CourseNotification::class, function ($n, $channels) {
            return $n->type == 'grade' && $n->params['grade'] == 18 && in_array('mail', $channels);
        });
    }

    public function test_student_can_turn_off_grade_emails()
    {
        $student = $this->makeStudent();

        $this->actingAs($student)->put('/notifications/settings', ['email' => ['assignment' => 1]]);

        $student->refresh();
        $this->assertFalse($student->wantsEmail('grade'));
        $this->assertTrue($student->wantsEmail('assignment'));
        $this->assertEquals(['database'], (new CourseNotification('grade', [], 'x', '/'))->via($student));
    }

    public function test_absent_notice_is_sent_once_even_if_saved_again()
    {
        Notification::fake();
        $teacher = $this->makeTeacher();
        $course = $this->makeCourse($teacher);
        $student = $this->makeStudent();
        $this->enroll($student, $course);
        $data = ['date' => '2026-10-20', 'time' => '09:00', 'status' => [$student->student->id => 'absent']];

        $this->actingAs($teacher)->post('/teach/courses/' . $course->id . '/attendance', $data);
        $this->actingAs($teacher)->post('/teach/courses/' . $course->id . '/attendance', $data);

        Notification::assertSentToTimes($student, CourseNotification::class, 1);
    }

    public function test_teacher_hears_about_new_submissions()
    {
        Notification::fake();
        $teacher = $this->makeTeacher();
        $student = $this->makeStudent();
        $course = $this->makeCourse($teacher);
        $this->enroll($student, $course);
        $assignment = Assignment::create(['course_id' => $course->id, 'title' => 'HW', 'due_at' => now()->addDay(), 'max_grade' => 20]);

        $this->actingAs($student)->post('/my/assignments/' . $assignment->id . '/submit', ['file' => UploadedFile::fake()->create('a.pdf', 20)]);

        Notification::assertSentTo($teacher, CourseNotification::class, function ($n) {
            return $n->type == 'submission';
        });
    }

    public function test_reminder_command_skips_students_who_handed_in_and_runs_once()
    {
        Notification::fake();
        $teacher = $this->makeTeacher();
        $course = $this->makeCourse($teacher);
        $late = $this->makeStudent();
        $done = $this->makeStudent();
        $this->enroll($late, $course);
        $this->enroll($done, $course);
        $assignment = Assignment::create(['course_id' => $course->id, 'title' => 'HW', 'due_at' => now()->addHours(10), 'max_grade' => 20]);
        Submission::create(['assignment_id' => $assignment->id, 'student_id' => $done->student->id, 'file' => 'x.pdf', 'original_name' => 'x.pdf']);
        Assignment::create(['course_id' => $course->id, 'title' => 'Later', 'due_at' => now()->addDays(5), 'max_grade' => 20]);

        $this->artisan('notify:due-reminders')->assertExitCode(0);
        $this->artisan('notify:due-reminders')->assertExitCode(0);

        Notification::assertSentToTimes($late, CourseNotification::class, 1);
        Notification::assertNotSentTo($done, CourseNotification::class);
    }

    public function test_opening_a_notification_marks_it_read()
    {
        $student = $this->makeStudent();
        $student->notify(new CourseNotification('lesson', ['title' => 'Intro'], 'Physics', '/my'));
        $id = $student->notifications()->first()->id;

        $this->actingAs($student)->get('/notifications')->assertOk()->assertSee('Intro');
        $this->actingAs($student)->get('/notifications/' . $id)->assertRedirect('/my');

        $this->assertEquals(0, $student->unreadNotifications()->count());
    }

    public function test_user_cannot_open_someone_elses_notification()
    {
        $owner = $this->makeStudent();
        $owner->notify(new CourseNotification('lesson', ['title' => 'Intro'], 'Physics', '/my'));
        $id = $owner->notifications()->first()->id;

        $this->actingAs($this->makeStudent())->get('/notifications/' . $id)->assertNotFound();
    }

    public function test_broken_mail_server_does_not_break_grading()
    {
        config(['mail.default' => 'smtp', 'mail.mailers.smtp.host' => '127.0.0.1', 'mail.mailers.smtp.port' => 1]);
        $teacher = $this->makeTeacher();
        $student = $this->makeStudent();
        $course = $this->makeCourse($teacher);
        $this->enroll($student, $course);
        $assignment = Assignment::create(['course_id' => $course->id, 'title' => 'HW', 'due_at' => now()->addDay(), 'max_grade' => 20]);
        $submission = Submission::create(['assignment_id' => $assignment->id, 'student_id' => $student->student->id, 'file' => 'x.pdf', 'original_name' => 'x.pdf']);

        $this->actingAs($teacher)->put('/teach/submissions/' . $submission->id, ['grade' => 15])
            ->assertSessionHas('success');

        $this->assertEquals(15, $submission->fresh()->grade);
    }
}
