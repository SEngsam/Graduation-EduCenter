<?php

namespace App\Http\Controllers\Teach;

use App\Support\Notifier;
use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Course;
use App\Notifications\CourseNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function show(Request $request, Course $course)
    {
        $this->checkCourse($course);

        $students = $course->students()->orderBy('name')->get();
        $slot = $course->slots()->where('day', now()->dayOfWeek)->first();

        $date = $request->input('date', today()->toDateString());
        if ($request->has('time')) {
            $time = $request->time;
        } elseif ($slot) {
            $time = substr($slot->starts_at, 0, 5);
        } else {
            $time = now()->format('H:00');
        }

        $session = ClassSession::where('course_id', $course->id)
            ->where('starts_at', Carbon::parse($date . ' ' . $time))
            ->first();

        $marks = [];
        if ($session) {
            foreach ($session->attendances as $attendance) {
                $marks[$attendance->student_id] = $attendance;
            }
        }

        $past = $course->sessions()->with('attendances')->take(8)->get();

        return view('teach.attendance', compact('course', 'students', 'session', 'marks', 'past', 'date', 'time', 'slot'));
    }

    public function store(Request $request, Course $course)
    {
        $this->checkCourse($course);

        $request->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'room' => 'nullable|string|max:100',
            'status' => 'required|array',
            'status.*' => 'required|in:present,late,absent,excused',
            'note.*' => 'nullable|string|max:255',
        ]);

        $session = ClassSession::firstOrCreate(
            ['course_id' => $course->id, 'starts_at' => Carbon::parse($request->date . ' ' . $request->time)],
            ['room' => $request->room]
        );

        $studentIds = $course->students()->pluck('students.id')->toArray();
        $notes = $request->input('note', []);

        foreach ($request->status as $studentId => $status) {
            if (!in_array($studentId, $studentIds)) {
                continue;
            }

            $attendance = Attendance::firstOrNew(['class_session_id' => $session->id, 'student_id' => $studentId]);
            $wasAbsent = $attendance->exists && $attendance->status == 'absent';

            $attendance->status = $status;
            $attendance->note = $notes[$studentId] ?? null;
            $attendance->save();

            if ($status == 'absent' && !$wasAbsent && $attendance->student->user) {
                Notifier::send($attendance->student->user, new CourseNotification('absent', [
                    'date' => $session->starts_at->translatedFormat('l j F، g:i a'),
                ], $course->title, route('student.course', $course->id)));
            }
        }

        return redirect()
            ->route('teach.attendance', [$course->id, 'date' => $request->date, 'time' => $request->time])
            ->with('success', __('teach.attendance_saved'));
    }
}
