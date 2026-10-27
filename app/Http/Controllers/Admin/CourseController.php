<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Registration;
use App\Models\Role;
use App\Models\Teacher;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('ability:all_courses')->only('index');
        $this->middleware('ability:add_course')->only(['create', 'store']);
        $this->middleware('ability:edit_course')->only(['edit', 'update']);
        $this->middleware('ability:delete_course')->only('destroy');
        $this->middleware('ability:assign_teacher')->only('assignTeacher');
    }

    public function index(Request $request)
    {
        $courses = Course::with('teacher.category')
            ->createdBy()
            ->withCount('students')
            ->when($request->q, function ($query, $q) {
                $query->where('title', 'like', '%' . $q . '%');
            })
            ->orderByDesc('id')
            ->paginate(10);

        $teachers = Teacher::orderBy('name')->get();

        return view('admin.courses.index', compact('courses', 'teachers'));
    }

    public function create()
    {
        $teachers = Teacher::orderBy('name')->get();

        return view('admin.courses.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'btn_text' => 'required|string|max:255',
            'btn_link' => 'required|string|max:255',
            'teacher_id' => 'nullable|integer|exists:teachers,id',
            'image' => 'required|image|max:2048',
        ]);

        $user = auth()->user();

        $course = new Course();
        $course->title = $request->title;
        $course->description = $request->description;
        $course->btn_text = $request->btn_text;
        $course->btn_link = $request->btn_link;
        $course->created_by = $user->id;
        $course->image = $this->saveImage($request, 'courses');

        if ($user->hasRole(Role::ROLE_TEACHER) && $user->teacher) {
            $course->teacher_id = $user->teacher->id;
        } else {
            $course->teacher_id = $request->teacher_id;
        }

        $course->save();

        return redirect()->route('admin.courses.index')->with('success', __('dash.saved_new'));
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        $teachers = Teacher::orderBy('name')->get();

        return view('admin.courses.edit', compact('course', 'teachers'));
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'btn_text' => 'required|string|max:255',
            'btn_link' => 'required|string|max:255',
            'teacher_id' => 'nullable|integer|exists:teachers,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $course->update([
            'title' => $request->title,
            'description' => $request->description,
            'btn_text' => $request->btn_text,
            'btn_link' => $request->btn_link,
            'teacher_id' => $request->teacher_id,
            'image' => $this->saveImage($request, 'courses', $course->image),
        ]);

        return redirect()->route('admin.courses.index')->with('success', __('dash.saved'));
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $this->deleteImage('courses', $course->image);
        $course->delete();

        return back()->with('success', __('dash.deleted'));
    }

    public function assignTeacher(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|integer|exists:teachers,id',
            'course_id' => 'required|integer|exists:courses,id',
        ]);

        $course = Course::find($request->course_id);
        $course->teacher_id = $request->teacher_id;
        $course->save();

        return back()->with('success', __('dash.teacher_assigned'));
    }

    public function enroll($course_id)
    {
        $user = auth()->user();

        if (!$user->hasRole(Role::ROLE_STUDENT) && !$user->hasAbility('enroll_courses')) {
            return back()->with('error', __('site.msg_only_students'));
        }

        $course = Course::find($course_id);
        if (!$course) {
            return back()->with('error', __('site.msg_course_missing'));
        }

        if (!$course->teacher_id) {
            return back()->with('error', __('site.msg_no_teacher_yet'));
        }

        $student = $user->studentProfile();

        $alreadyEnrolled = Registration::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($alreadyEnrolled) {
            return back()->with('error', __('site.msg_already_enrolled'));
        }

        Registration::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);

        return back()->with('success', __('site.msg_enrolled'));
    }

    public function removeEnrollment($courseId)
    {
        $student = auth()->user()->student;

        if (!$student) {
            return back()->with('error', __('site.msg_no_student'));
        }

        $deleted = Registration::where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->delete();

        if ($deleted) {
            return back()->with('success', __('site.msg_dropped'));
        }

        return back()->with('error', __('site.msg_not_enrolled'));
    }
}
