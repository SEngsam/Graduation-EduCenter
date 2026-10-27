<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassSession;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    private function findSession($student, $code)
    {
        if (!$student || !preg_match('/^[0-9]{6}$/', $code)) {
            return null;
        }

        $courseIds = $student->courses()->pluck('courses.id');
        $sessions = ClassSession::whereIn('course_id', $courseIds)->where('qr_open', true)->get();

        foreach ($sessions as $session) {
            if ($session->checkCode($code)) {
                return $session;
            }
        }

        return null;
    }

    public function show(Request $request)
    {
        $code = str_replace(' ', '', (string) $request->code);
        $session = null;

        if ($code) {
            $session = $this->findSession(auth()->user()->student, $code);
        }

        return view('checkin.show', compact('code', 'session'));
    }

    public function store(Request $request)
    {
        $student = auth()->user()->student;
        $code = str_replace(' ', '', (string) $request->code);
        $session = $this->findSession($student, $code);

        if (!$session) {
            return redirect()->route('attendance.here')->with('error', __('qr.wrong_code'));
        }

        $attendance = Attendance::firstOrNew(['class_session_id' => $session->id, 'student_id' => $student->id]);

        if (!$attendance->exists || $attendance->status == 'absent') {
            $status = 'present';
            if (now()->greaterThan($session->starts_at->copy()->addMinutes(ClassSession::LATE_AFTER))) {
                $status = 'late';
            }

            $attendance->status = $status;
            $attendance->save();
        }

        $sessionIds = $session->course->sessions()->pluck('id');
        $statuses = $student->attendances()->whereIn('class_session_id', $sessionIds)->pluck('status');
        $came = 0;
        foreach ($statuses as $item) {
            if ($item == 'present' || $item == 'late') {
                $came++;
            }
        }
        $rate = count($statuses) ? round($came * 100 / count($statuses)) : null;

        return view('checkin.done', compact('session', 'attendance', 'rate'));
    }
}
