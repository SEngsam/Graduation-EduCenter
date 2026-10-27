<?php

namespace App\Http\Controllers;

use App\Support\Notifier;
use App\Models\Course;
use App\Models\CourseQuestion;
use App\Models\QuestionReply;
use App\Notifications\CourseNotification;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    private function checkAccess(Course $course)
    {
        $user = auth()->user();

        if ($course->isTaughtBy($user)) {
            return true;
        }

        if (!$user->student || !$course->students()->where('students.id', $user->student->id)->exists()) {
            abort(403);
        }

        return false;
    }

    public function index(Request $request, Course $course)
    {
        $isTeacher = $this->checkAccess($course);

        $questions = $course->questions()->with(['user', 'lesson', 'replies.user'])->get();

        $current = null;
        if ($request->q) {
            $current = $questions->firstWhere('id', $request->q);
        }
        if (!$current) {
            $current = $questions->first();
        }

        $lessons = $course->lessons()->where('is_published', true)->orderBy('id')->get(['id', 'title']);

        return view('discussion.index', compact('course', 'questions', 'current', 'lessons', 'isTeacher'));
    }

    public function store(Request $request, Course $course)
    {
        $isTeacher = $this->checkAccess($course);

        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'nullable|string|max:3000',
            'lesson_id' => 'nullable|integer',
        ]);

        $lessonId = null;
        if ($request->lesson_id && $course->lessons()->where('id', $request->lesson_id)->exists()) {
            $lessonId = $request->lesson_id;
        }

        $question = CourseQuestion::create([
            'course_id' => $course->id,
            'lesson_id' => $lessonId,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'body' => $request->body,
        ]);

        if (!$isTeacher && $course->teacher && $course->teacher->user) {
            Notifier::send($course->teacher->user, new CourseNotification('question', [
                'name' => auth()->user()->name,
                'title' => $question->title,
            ], $course->title, route('discussion.index', [$course, 'q' => $question->id])));
        }

        return redirect()->route('discussion.index', [$course, 'q' => $question->id])->with('success', __('discussion.asked'));
    }

    public function reply(Request $request, CourseQuestion $question)
    {
        $course = $question->course;
        $this->checkAccess($course);

        $request->validate([
            'body' => 'required|string|max:3000',
        ]);

        QuestionReply::create([
            'course_question_id' => $question->id,
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);

        if ($question->user_id != auth()->id() && $question->user->id) {
            Notifier::send($question->user, new CourseNotification('reply', [
                'name' => auth()->user()->name,
                'title' => $question->title,
            ], $course->title, route('discussion.index', [$course, 'q' => $question->id])));
        }

        return redirect()->route('discussion.index', [$course, 'q' => $question->id]);
    }

    public function destroy(CourseQuestion $question)
    {
        $course = $question->course;

        if (!$course->isTaughtBy(auth()->user()) && $question->user_id != auth()->id()) {
            abort(403);
        }

        $question->delete();

        return redirect()->route('discussion.index', $course)->with('success', __('discussion.deleted'));
    }

    public function destroyReply(QuestionReply $reply)
    {
        $question = $reply->question;

        if (!$question->course->isTaughtBy(auth()->user()) && $reply->user_id != auth()->id()) {
            abort(403);
        }

        $reply->delete();

        return redirect()->route('discussion.index', [$question->course_id, 'q' => $question->id])->with('success', __('discussion.deleted'));
    }
}
