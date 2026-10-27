<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\CourseSlot;
use App\Models\Lesson;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $student = $user->student;
        $studentId = $student ? $student->id : $user->id;

        $courses = Registration::where('student_id', $studentId)
            ->with('course.teacher.category')
            ->latest()
            ->get()
            ->pluck('course')
            ->filter();

        $courseIds = $courses->pluck('id');

        $todayClasses = CourseSlot::with('course')
            ->whereIn('course_id', $courseIds)
            ->where('day', now()->dayOfWeek)
            ->orderBy('starts_at')
            ->get();

        $upcoming = collect();
        $attendanceRate = null;
        $average = null;
        $progress = [];

        if ($student) {
            $handedIn = $student->submissions()->pluck('assignment_id');

            $upcoming = Assignment::with('course')
                ->whereIn('course_id', $courseIds)
                ->where('due_at', '>', now())
                ->whereNotIn('id', $handedIn)
                ->orderBy('due_at')
                ->get();

            $statuses = $student->attendances()->pluck('status');
            if (count($statuses) > 0) {
                $came = $statuses->filter(function ($status) {
                    return $status == 'present' || $status == 'late';
                })->count();
                $attendanceRate = round($came * 100 / count($statuses));
            }

            $graded = $student->submissions()->whereNotNull('grade')->with('assignment')->get();
            if (count($graded) > 0) {
                $total = 0;
                foreach ($graded as $submission) {
                    $total += $submission->grade * 20 / $submission->assignment->max_grade;
                }
                $average = round($total / count($graded), 1);
            }

            foreach ($courses as $course) {
                $lessonIds = Lesson::where('course_id', $course->id)->where('is_published', true)->pluck('id');
                $doneCount = $student->completedLessons()->whereIn('lessons.id', $lessonIds)->count();
                $progress[$course->id] = count($lessonIds) ? round($doneCount * 100 / count($lessonIds)) : 0;
            }
        }

        $events = Event::orderByDesc('id')->take(3)->get();

        return view('student.dashboard', compact('courses', 'todayClasses', 'upcoming', 'attendanceRate', 'average', 'progress', 'events'));
    }

    public function profile()
    {
        return view('student.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_phone' => ['nullable', 'string', 'max:30', Rule::unique('students', 'no_phone')->ignore(optional($student)->id)],
            'major' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update(['name' => $data['name'], 'email' => $data['email']]);

        if ($student) {
            $student->update([
                'name' => $data['name'],
                'no_phone' => $data['no_phone'] ?? $student->no_phone,
                'major' => $data['major'] ?? $student->major,
            ]);
        }

        return back()->with('success', __('site.profile_saved'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success', __('site.password_saved'));
    }
}
