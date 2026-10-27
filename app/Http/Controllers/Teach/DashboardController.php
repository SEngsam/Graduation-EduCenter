<?php

namespace App\Http\Controllers\Teach;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\CourseSlot;
use App\Models\Registration;
use App\Models\Submission;

class DashboardController extends Controller
{
    public function index()
    {
        $courses = $this->myCourses()->withCount(['students', 'lessons'])->with('slots')->get();
        $courseIds = $courses->pluck('id');

        $today = CourseSlot::with('course')
            ->whereIn('course_id', $courseIds)
            ->where('day', now()->dayOfWeek)
            ->orderBy('starts_at')
            ->get();

        $todaySessions = ClassSession::whereIn('course_id', $courseIds)
            ->whereDate('starts_at', today())
            ->has('attendances')
            ->get();

        $taken = [];
        foreach ($todaySessions as $session) {
            $taken[] = $session->course_id . '-' . $session->starts_at->format('H:i');
        }

        $pending = Submission::with(['student', 'assignment'])
            ->whereNull('grade')
            ->whereHas('assignment', function ($q) use ($courseIds) {
                $q->whereIn('course_id', $courseIds);
            })
            ->latest()
            ->get();

        $sessionIds = ClassSession::whereIn('course_id', $courseIds)
            ->where('starts_at', '>=', now()->startOfMonth())
            ->pluck('id');

        $total = Attendance::whereIn('class_session_id', $sessionIds)->count();
        $came = Attendance::whereIn('class_session_id', $sessionIds)->whereIn('status', ['present', 'late'])->count();
        $attendanceRate = $total > 0 ? round($came * 100 / $total) : null;

        $studentsCount = Registration::whereIn('course_id', $courseIds)->distinct('student_id')->count('student_id');

        return view('teach.dashboard', compact('courses', 'today', 'taken', 'pending', 'attendanceRate', 'studentsCount'));
    }
}
