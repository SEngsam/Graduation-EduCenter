<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\Role;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Gate::authorize('all_courses');
        $user = Auth::user();
        $courses = Course::with('teacher.category')->CreatedBy()->withCount('students')
            ->when(request('q'), function ($query, $q) {
                $query->where('title', 'like', "%{$q}%");
            })
            ->orderByDesc('id')
            ->paginate(10);
        $teachers = Teacher::all();
        return view('admin.courses.index', compact('courses', 'teachers','user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $teachers = Teacher::all();
        $courses = Course::all();
        return view('admin.courses.create', compact('courses', 'teachers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'image' => 'required',
            'title' => 'required',
            'description' => 'required',
            'btn_text' => 'required',
            'btn_link' => 'required',
            'teacher_id' => ['nullable', 'integer', Rule::exists('teachers', 'id')],
        ]);
        $data = [];
        $user =Auth::user()->load('teacher');
        $img = $request->file('image');
        $img_name = rand() . time() . $img->getClientOriginalName();
        $img->move(public_path('uploads/courses'), $img_name);
        $data = [
            'image' => $img_name,
            'title' => $request->title,
            'description' => $request->description,
            'btn_text' => $request->btn_text,
            'btn_link' => $request->btn_link,
            'created_by' => $user->id,
        ];
       
     
        if($user->hasRole(Role::ROLE_TEACHER)){
            $data['teacher_id'] = $user->teacher->id;
        } elseif ($request->filled('teacher_id')) {
            $data['teacher_id'] = $request->teacher_id;
        }
       
        Course::create($data);

        return redirect()->route('admin.courses.index')->with('msg', __('dash.saved_new'))->with('type', 'success');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $teachers = Teacher::all();
        $courses = Course::all();
        $course = Course::findOrFail($id);
        return view('admin.courses.edit', compact('courses', 'course', 'teachers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'btn_text' => 'required',
            'btn_link' => 'required',
            //  'registration_id' => 'required',
            'teacher_id' => ['nullable', 'integer', Rule::exists('teachers', 'id')],
        ]);

        $courses = Course::findOrFail($id);

        $img_name = $courses->image;
        if ($request->hasFile('image')) {
            $img_name = rand() . time() . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads/courses'), $img_name);
        }


        $courses->update([
            'image' => $img_name,
            'title' => $request->title,
            'description' => $request->description,
            'btn_text' => $request->btn_text,
            'btn_link' => $request->btn_link,
            // 'registration_id' => $request->registration_id,
            'teacher_id' => $request->teacher_id,

        ]);

        // Redirect
        return redirect()->route('admin.courses.index')->with('msg', __('dash.saved'))->with('type', 'info');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        File::delete(public_path('uploads/courses/' . $course->image));
        $course->delete();
        return redirect()->back()->with('msg', __('dash.deleted'))->with('type', 'danger');
    }
    public function enroll($course_id)
    {
        $user = auth()->user();

        if (!$user->hasRole(Role::ROLE_STUDENT) && !$user->hasAbility('enroll_courses')) {
            return redirect()->back()->with('error', __('site.msg_only_students'));
        }

        $course = Course::find($course_id);
        if (!$course) {
            return redirect()->back()->with('error', __('site.msg_course_missing'));
        }

        if (!$course->teacher_id) {
            return redirect()->back()->with('error', __('site.msg_no_teacher_yet'));
        }

        $student = $user->studentProfile();

        $alreadyEnrolled = Registration::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($alreadyEnrolled) {
            return redirect()->back()->with('error', __('site.msg_already_enrolled'));
        }

        Registration::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);

        return redirect()->back()->with('success', __('site.msg_enrolled'));
    }

    public function removeEnrollment($courseId)
    {
        $student = auth()->user()->student;

        if (!$student) {
            return redirect()->back()->with('error', __('site.msg_no_student'));
        }

        $deleted = Registration::where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->delete();

        if ($deleted) {
            return redirect()->back()->with('success', __('site.msg_dropped'));
        }

        return redirect()->back()->with('error', __('site.msg_not_enrolled'));
    }

    public function assignTeacher(Request $request)
    {
        $request->validate([
            'teacher_id' => ['required', 'integer', Rule::exists('teachers', 'id')],
            'course_id' => ['required', 'integer', Rule::exists('courses', 'id')],
        ]);
        $course = Course::find($request->course_id);

        if ($course) {
            $course->teacher_id = $request->teacher_id;
            $course->save();

            return redirect()->back()->with('success', __('dash.teacher_assigned'));
        }

        return redirect()->route('admin.courses.index')->with('error', __('site.msg_course_missing'));
    }
}
