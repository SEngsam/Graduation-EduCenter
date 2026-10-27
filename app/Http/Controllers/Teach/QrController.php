<?php

namespace App\Http\Controllers\Teach;

use App\Support\Notifier;
use App\Support\Qr;
use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Course;
use App\Notifications\CourseNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QrController extends Controller
{
    public function open(Request $request, Course $course)
    {
        $this->checkCourse($course);

        $request->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'room' => 'nullable|string|max:100',
        ]);

        $session = ClassSession::firstOrCreate(
            ['course_id' => $course->id, 'starts_at' => Carbon::parse($request->date . ' ' . $request->time)],
            ['room' => $request->room]
        );

        $session->qr_secret = Str::random(32);
        $session->qr_open = true;
        $session->save();

        return redirect()->route('teach.qr', $session);
    }

    public function show(ClassSession $session)
    {
        $this->checkCourse($session->course);

        if (!$session->qr_open) {
            return redirect()->route('teach.attendance', [$session->course_id, 'date' => $session->starts_at->toDateString(), 'time' => $session->starts_at->format('H:i')]);
        }

        $course = $session->course;
        $total = $course->students()->count();

        return view('teach.qr', compact('session', 'course', 'total'));
    }

    public function status(ClassSession $session)
    {
        $this->checkCourse($session->course);

        $code = $session->qrCode();

        $arrived = [];
        $list = $session->attendances()->with('student')->whereIn('status', ['present', 'late'])->orderBy('updated_at')->get();
        foreach ($list as $attendance) {
            $arrived[] = [
                'name' => $attendance->student->name ?? '',
                'time' => $attendance->updated_at->format('g:i'),
                'late' => $attendance->status == 'late',
            ];
        }

        return response()->json([
            'open' => $session->qr_open,
            'code' => $code,
            'svg' => Qr::svg(route('attendance.here', ['code' => $code])),
            'seconds' => 60 - time() % 60,
            'arrived' => $arrived,
        ]);
    }

    public function close(ClassSession $session)
    {
        $course = $session->course;
        $this->checkCourse($course);

        $session->qr_open = false;
        $session->save();

        $marked = $session->attendances()->pluck('student_id')->toArray();

        foreach ($course->students as $student) {
            if (in_array($student->id, $marked)) {
                continue;
            }

            Attendance::create([
                'class_session_id' => $session->id,
                'student_id' => $student->id,
                'status' => 'absent',
            ]);

            if ($student->user) {
                Notifier::send($student->user, new CourseNotification('absent', [
                    'date' => $session->starts_at->translatedFormat('l j F، g:i a'),
                ], $course->title, route('student.course', $course->id)));
            }
        }

        return redirect()
            ->route('teach.attendance', [$course->id, 'date' => $session->starts_at->toDateString(), 'time' => $session->starts_at->format('H:i')])
            ->with('success', __('qr.closed'));
    }
}
