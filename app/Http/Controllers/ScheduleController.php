<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSlot;
use App\Models\Registration;
use App\Models\Role;

class ScheduleController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole(Role::ROLE_STUDENT)) {
            $courseIds = [];
            if ($user->student) {
                $courseIds = Registration::where('student_id', $user->student->id)->pluck('course_id');
            }
            $layout = 'layouts.student';
        } elseif ($user->teacher) {
            $courseIds = Course::where('teacher_id', $user->teacher->id)->pluck('id');
            $layout = 'layouts.teacher';
        } else {
            $courseIds = Course::pluck('id');
            $layout = 'layouts.teacher';
        }

        $slots = CourseSlot::with('course.teacher.category')
            ->whereIn('course_id', $courseIds)
            ->orderBy('starts_at')
            ->get();

        $firstHour = 8;
        $lastHour = 16;
        foreach ($slots as $slot) {
            $firstHour = min($firstHour, intdiv($slot->minutes(), 60));
            $lastHour = max($lastHour, (int) ceil($slot->minutes('ends_at') / 60));
        }

        $days = [6, 0, 1, 2, 3, 4];
        foreach ($slots as $slot) {
            if ($slot->day == 5 && !in_array(5, $days)) {
                $days[] = 5;
            }
        }

        return view('schedule.index', compact('slots', 'firstHour', 'lastHour', 'days', 'layout'));
    }
}
