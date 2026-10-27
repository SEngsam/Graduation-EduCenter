<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\ClassSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->makeRoles();
    }

    private function openSession($startsAt)
    {
        $teacher = $this->makeTeacher();
        $course = $this->makeCourse($teacher);
        $student = $this->makeStudent('سارة');
        $this->enroll($student, $course);

        $this->actingAs($teacher)->post('/teach/courses/' . $course->id . '/qr', [
            'date' => $startsAt->toDateString(),
            'time' => $startsAt->format('H:i'),
        ])->assertRedirect();

        return [$teacher, $course, $student, ClassSession::first()];
    }

    public function test_student_checks_in_with_the_current_code()
    {
        [$teacher, $course, $student, $session] = $this->openSession(now());

        $this->assertTrue($session->qr_open);

        $this->actingAs($student)->get('/here?code=' . $session->qrCode())
            ->assertOk()
            ->assertSee($course->title);

        $this->actingAs($student)->post('/here', ['code' => $session->qrCode()])->assertOk();

        $this->assertEquals('present', Attendance::first()->status);
    }

    public function test_previous_minute_code_still_works_but_older_does_not()
    {
        [$teacher, $course, $student, $session] = $this->openSession(now());
        $minute = floor(time() / 60);

        $this->assertTrue($session->checkCode($session->qrCode($minute - 1)));
        $this->assertFalse($session->checkCode($session->qrCode($minute - 5)));
    }

    public function test_wrong_code_does_not_check_in()
    {
        [$teacher, $course, $student, $session] = $this->openSession(now());

        $wrong = $session->qrCode() == '000000' ? '111111' : '000000';

        $this->actingAs($student)->post('/here', ['code' => $wrong])->assertRedirect('/here');
        $this->assertEquals(0, Attendance::count());
    }

    public function test_checking_in_after_ten_minutes_is_late()
    {
        [$teacher, $course, $student, $session] = $this->openSession(now()->subMinutes(20));

        $this->actingAs($student)->post('/here', ['code' => $session->qrCode()])->assertOk();

        $this->assertEquals('late', Attendance::first()->status);
    }

    public function test_student_from_another_course_cannot_use_the_code()
    {
        [$teacher, $course, $student, $session] = $this->openSession(now());
        $other = $this->makeStudent();

        $this->actingAs($other)->post('/here', ['code' => $session->qrCode()])->assertRedirect('/here');
        $this->assertEquals(0, Attendance::count());
    }

    public function test_closing_marks_the_rest_absent_and_stops_the_code()
    {
        [$teacher, $course, $student, $session] = $this->openSession(now());
        $late = $this->makeStudent('عمر');
        $this->enroll($late, $course);

        $this->actingAs($student)->post('/here', ['code' => $session->qrCode()]);

        $this->actingAs($teacher)->post('/teach/sessions/' . $session->id . '/qr/close')->assertRedirect();

        $this->assertEquals('present', Attendance::where('student_id', $student->student->id)->first()->status);
        $this->assertEquals('absent', Attendance::where('student_id', $late->student->id)->first()->status);
        $this->assertEquals('absent', $late->notifications()->first()->data['type']);

        $session->refresh();
        $this->assertFalse($session->checkCode($session->qrCode()));
    }

    public function test_status_endpoint_is_only_for_the_teacher()
    {
        [$teacher, $course, $student, $session] = $this->openSession(now());

        $this->actingAs($teacher)->get('/teach/sessions/' . $session->id . '/qr/status')
            ->assertOk()
            ->assertJson(['open' => true, 'code' => $session->qrCode()]);

        $this->actingAs($this->makeTeacher('غريب'))->get('/teach/sessions/' . $session->id . '/qr/status')->assertForbidden();
    }
}
