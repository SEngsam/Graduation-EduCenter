<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentCourseController extends Controller
{
    private function getStudent(Course $course)
    {
        $student = auth()->user()->student;

        if (!$student || !$course->enrolled()->where('students.id', $student->id)->exists()) {
            abort(403);
        }

        return $student;
    }

    public function show(Course $course)
    {
        $student = $this->getStudent($course);

        $course->load(['teacher', 'slots']);
        $units = $course->units()->with(['lessons' => function ($q) {
            $q->where('is_published', true);
        }])->get();
        $loose = $course->lessons()->where('is_published', true)->whereNull('course_unit_id')->get();

        $lessonIds = $course->lessons()->where('is_published', true)->pluck('id');
        $done = $student->completedLessons()->whereIn('lessons.id', $lessonIds)->pluck('lessons.id')->toArray();

        $progress = 0;
        if (count($lessonIds) > 0) {
            $progress = round(count($done) * 100 / count($lessonIds));
        }

        $next = $course->lessons()->where('is_published', true)->whereNotIn('id', $done)->first();

        $assignments = $course->assignments;
        $submissions = [];
        foreach ($student->submissions()->whereIn('assignment_id', $assignments->pluck('id'))->get() as $submission) {
            $submissions[$submission->assignment_id] = $submission;
        }

        $upcoming = null;
        foreach ($assignments as $assignment) {
            if (!$assignment->isOverdue() && !isset($submissions[$assignment->id])) {
                $upcoming = $assignment;
                break;
            }
        }

        $sessionIds = $course->sessions()->pluck('id');
        $attendance = $student->attendances()->whereIn('class_session_id', $sessionIds)->pluck('status');

        return view('student.course', compact('course', 'units', 'loose', 'done', 'progress', 'next', 'assignments', 'submissions', 'upcoming', 'attendance'));
    }

    public function toggleLesson(Lesson $lesson)
    {
        if (!$lesson->is_published) {
            abort(404);
        }

        $student = $this->getStudent($lesson->course);
        $student->completedLessons()->toggle($lesson->id);

        return back();
    }

    public function lessonFile(Lesson $lesson)
    {
        if (!$lesson->is_published || !$lesson->file) {
            abort(404);
        }

        $this->getStudent($lesson->course);

        $ext = pathinfo($lesson->file, PATHINFO_EXTENSION);

        return Storage::download($lesson->file, $lesson->title . '.' . $ext);
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $student = $this->getStudent($assignment->course);

        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,zip,jpg,png|max:10240',
        ]);

        $submission = $student->submissions()->where('assignment_id', $assignment->id)->first();

        if ($submission && $submission->grade !== null) {
            return back()->with('error', __('teach.already_graded'));
        }

        if ($submission) {
            Storage::delete($submission->file);
        } else {
            $submission = new \App\Models\Submission();
            $submission->assignment_id = $assignment->id;
            $submission->student_id = $student->id;
        }

        $submission->file = $request->file('file')->store('submissions');
        $submission->original_name = $request->file('file')->getClientOriginalName();
        $submission->save();

        return back()->with('success', __('teach.submitted'));
    }
}
