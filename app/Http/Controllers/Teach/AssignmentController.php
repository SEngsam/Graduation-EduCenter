<?php

namespace App\Http\Controllers\Teach;

use App\Support\Notifier;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Notifications\CourseNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    public function index(Request $request, Course $course)
    {
        $this->checkCourse($course);

        $assignments = $course->assignments()->withCount('submissions')->get();
        $students = $course->students()->orderBy('name')->get();

        $current = null;
        if ($request->a) {
            $current = $assignments->where('id', $request->a)->first();
        }
        if (!$current) {
            $current = $assignments->first();
        }

        $submissions = [];
        if ($current) {
            foreach ($current->submissions as $submission) {
                $submissions[$submission->student_id] = $submission;
            }
        }

        $filter = $request->input('show', 'all');

        return view('teach.assignments', compact('course', 'assignments', 'current', 'students', 'submissions', 'filter'));
    }

    public function store(Request $request, Course $course)
    {
        $this->checkCourse($course);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'due_at' => 'required|date',
            'max_grade' => 'required|integer|between:1,100',
        ]);

        $assignment = $course->assignments()->create($request->only('title', 'description', 'due_at', 'max_grade'));

        $course->notifyStudents('assignment', [
            'title' => $assignment->title,
            'due' => $assignment->due_at->translatedFormat('l j F'),
        ], route('student.course', $course->id));

        return redirect()
            ->route('teach.assignments', [$course->id, 'a' => $assignment->id])
            ->with('success', __('teach.assignment_added'));
    }

    public function destroy(Assignment $assignment)
    {
        $course = $assignment->course;
        $this->checkCourse($course);

        foreach ($assignment->submissions as $submission) {
            Storage::delete($submission->file);
        }
        $assignment->delete();

        return redirect()->route('teach.assignments', $course->id)->with('success', __('dash.deleted'));
    }

    public function grade(Request $request, Submission $submission)
    {
        $this->checkCourse($submission->assignment->course);

        $request->validate([
            'grade' => 'required|numeric|min:0|max:' . $submission->assignment->max_grade,
            'feedback' => 'nullable|string|max:2000',
        ]);

        $submission->grade = $request->grade;
        $submission->feedback = $request->feedback;
        $submission->graded_at = now();
        $submission->save();

        $assignment = $submission->assignment;
        if ($submission->student->user) {
            Notifier::send($submission->student->user, new CourseNotification('grade', [
                'title' => $assignment->title,
                'grade' => (float) $submission->grade,
                'max' => $assignment->max_grade,
                'feedback' => $submission->feedback,
            ], $assignment->course->title, route('student.course', $assignment->course_id)));
        }

        return back()->with('success', __('teach.graded_msg', ['name' => $submission->student->name]));
    }

    public function download(Submission $submission)
    {
        $this->checkCourse($submission->assignment->course);

        return Storage::download($submission->file, $submission->original_name);
    }
}
