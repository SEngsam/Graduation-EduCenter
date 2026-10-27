<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\About;
use App\Models\Event;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Category;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        $courses = Course::withCount('students')
            ->with(['teacher.category'])
            ->orderByDesc('id')
            ->take(6)
            ->get();

        $courses = $courses->map(function ($course) {
            $course->is_enrolled = $this->isEnrolled($course->id);
            return $course;
        });
        $events = Event::orderByDesc('id')->take(2)->get();
        $teachers = Teacher::withCount('course')->orderByDesc('id')->take(3)->get();
        $news = News::orderByDesc('id')->first();
        $categories = Category::take(3)->get();

        return view('site.index', compact('courses', 'events', 'teachers', 'news', 'categories'));
    }
    public function about()
    {
        $abouts = About::orderByDesc('id')->take(1)->get();
        $teachers = Teacher::orderByDesc('id')->take(3)->get();

        return view('site.about', compact('abouts', 'teachers'));
    }
    public function courses(Request $request)
    {
        $courses = Course::withCount('students')
            ->with('teacher.category')
            ->when($request->q, function ($query, $q) {
                $query->where('title', 'like', "%{$q}%");
            })
            ->when($request->teacher, function ($query, $teacher) {
                $query->where('teacher_id', $teacher);
            })
            ->orderByDesc('id')
            ->paginate(9)
            ->withQueryString();

        $courses->getCollection()->transform(function ($course) {
            $course->is_enrolled = $this->isEnrolled($course->id);
            return $course;
        });

        return view('site.course', compact('courses'));
    }

    public function course($id)
    {
        $course = Course::withCount('students')->with('teacher.category')->findOrFail($id);
        $course->is_enrolled = $this->isEnrolled($course->id);

        $related = Course::with('teacher.category')
            ->where('id', '!=', $course->id)
            ->when($course->teacher, function ($query) use ($course) {
                $query->whereHas('teacher', function ($q) use ($course) {
                    $q->where('categorie_id', $course->teacher->categorie_id);
                });
            })
            ->take(3)
            ->get();

        return view('site.course-show', compact('course', 'related'));
    }

    public function teachers()
    {
        $categories = Category::orderByDesc('id')->take(8)->get();
        $teachers = Teacher::with(['category', 'course'])->orderByDesc('id')->take(9)->get();
        return view('site.teacher', compact('categories', 'teachers'));
    }

    public function event()
    {
        $events = Event::orderByDesc('id')->take(6)->get();
        return view('site.event', compact('events'));
    }

    public function contact()
    {
        return view('site.contact');
    }

    public function contact_data(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mail' => 'required|email|max:255',
            'major' => 'nullable|string|max:255',
            'message' => 'required|string|max:3000',
        ]);

        ContactMessage::create([
            'name' => $request->name,
            'email' => $request->mail,
            'major' => $request->major,
            'message' => $request->message,
            'user_id' => auth()->id(),
        ]);

        $name = $request->name;
        $mail = $request->mail;
        $major = $request->major;
        $message = $request->message;

        return view('site.contact_data', compact('name', 'mail', 'major', 'message'));
    }

    private function isEnrolled($courseId)
    {
        if (!auth()->check()) {
            return false; 
        }
        $student = auth()->user()->student;
        if (!$student) {
            return false;
        }

        return \App\Models\Registration::where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->exists();
    }
}
