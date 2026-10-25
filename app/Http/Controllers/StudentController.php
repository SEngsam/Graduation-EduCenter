<?php

namespace App\Http\Controllers;

use App\Models\News;
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
        $studentId = $user->student->id ?? $user->id;

        $courses = Registration::where('student_id', $studentId)
            ->with('course.teacher.category')
            ->latest()
            ->get()
            ->pluck('course')
            ->filter();

        $events = Event::orderByDesc('id')->take(3)->get();
        $news = News::orderByDesc('id')->take(3)->get();

        return view('student.dashboard', [
            'courses' => $courses,
            'events' => $events,
            'news' => $news,
            'teachersCount' => $courses->pluck('teacher_id')->filter()->unique()->count(),
        ]);
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
