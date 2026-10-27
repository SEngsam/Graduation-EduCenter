<?php

namespace App\Http\Controllers\Teach;

use App\Models\Course;
use App\Models\CourseSlot;
use App\Models\CourseUnit;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function show(Course $course)
    {
        $this->checkCourse($course);

        $course->load(['units.lessons', 'slots']);
        $course->loadCount(['students', 'lessons']);
        $loose = $course->lessons()->whereNull('course_unit_id')->get();

        return view('teach.course', compact('course', 'loose'));
    }

    public function storeUnit(Request $request, Course $course)
    {
        $this->checkCourse($course);

        $request->validate(['title' => 'required|string|max:255']);

        $course->units()->create([
            'title' => $request->title,
            'position' => $course->units()->count() + 1,
        ]);

        return back()->with('success', __('teach.unit_added'));
    }

    public function destroyUnit(CourseUnit $unit)
    {
        $this->checkCourse($unit->course);
        $unit->delete();

        return back()->with('success', __('dash.deleted'));
    }

    public function storeLesson(Request $request, Course $course)
    {
        $this->checkCourse($course);

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,file,link,text',
            'url' => 'nullable|required_if:type,video,link|url|max:500',
            'body' => 'nullable|required_if:type,text|string|max:10000',
            'file' => 'nullable|required_if:type,file|file|mimes:pdf,ppt,pptx,doc,docx,zip|max:20480',
            'course_unit_id' => 'nullable|exists:course_units,id',
        ]);

        $lesson = new Lesson();
        $lesson->course_id = $course->id;
        $lesson->course_unit_id = $request->course_unit_id;
        $lesson->title = $request->title;
        $lesson->type = $request->type;
        $lesson->url = $request->url;
        $lesson->body = $request->body;
        $lesson->is_published = $request->has('is_published');
        $lesson->position = $course->lessons()->count() + 1;

        if ($request->hasFile('file')) {
            $lesson->file = $request->file('file')->store('lessons');
        }

        $lesson->save();

        return back()->with('success', __('teach.lesson_added'));
    }

    public function togglePublish(Lesson $lesson)
    {
        $this->checkCourse($lesson->course);

        $lesson->is_published = !$lesson->is_published;
        $lesson->save();

        if ($lesson->is_published) {
            return back()->with('success', __('teach.lesson_published'));
        }

        return back()->with('success', __('teach.lesson_hidden'));
    }

    public function destroyLesson(Lesson $lesson)
    {
        $this->checkCourse($lesson->course);

        if ($lesson->file) {
            Storage::delete($lesson->file);
        }
        $lesson->delete();

        return back()->with('success', __('dash.deleted'));
    }

    public function storeSlot(Request $request, Course $course)
    {
        $this->checkCourse($course);

        $request->validate([
            'day' => 'required|integer|between:0,6',
            'starts_at' => 'required|date_format:H:i',
            'ends_at' => 'required|date_format:H:i|after:starts_at',
            'room' => 'nullable|string|max:100',
        ]);

        $course->slots()->create($request->only('day', 'starts_at', 'ends_at', 'room'));

        return back()->with('success', __('teach.slot_added'));
    }

    public function destroySlot(CourseSlot $slot)
    {
        $this->checkCourse($slot->course);
        $slot->delete();

        return back()->with('success', __('dash.deleted'));
    }
}
