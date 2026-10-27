<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Event;
use App\Models\Major;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\Role;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();

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

        $teachingCourses = $user->hasRole(Role::ROLE_TEACHER) && $user->teacher
            ? Course::withCount('students')->where('teacher_id', $user->teacher->id)->get()
            : collect();

        return view('admin.index', compact('user', 'stats', 'registrations', 'unassigned', 'teachers', 'teachingCourses'));
    }
}
