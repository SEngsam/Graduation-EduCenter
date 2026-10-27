<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Event;
use App\Models\Registration;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;

class AdminController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole(Role::ROLE_STUDENT)) {
            return redirect()->route('student.dashboard');
        }

        $stats = [
            'students' => Student::count(),
            'courses' => Course::count(),
            'teachers' => Teacher::count(),
            'categories' => Category::count(),
            'events' => Event::count(),
        ];

        $registrations = Registration::with(['student', 'course.teacher'])
            ->latest()
            ->take(6)
            ->get();

        $unassigned = Course::whereNull('teacher_id')->orderByDesc('id')->take(5)->get();
        $teachers = Teacher::orderBy('name')->get(['id', 'name']);

        $teachingCourses = collect();
        if ($user->hasRole(Role::ROLE_TEACHER) && $user->teacher) {
            $teachingCourses = Course::withCount('students')->where('teacher_id', $user->teacher->id)->get();
        }

        return view('admin.index', compact('user', 'stats', 'registrations', 'unassigned', 'teachers', 'teachingCourses'));
    }
}
