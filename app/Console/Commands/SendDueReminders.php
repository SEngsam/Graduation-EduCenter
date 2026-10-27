<?php

namespace App\Console\Commands;

use App\Support\Notifier;
use App\Models\Assignment;
use App\Notifications\CourseNotification;
use Illuminate\Console\Command;

class SendDueReminders extends Command
{
    protected $signature = 'notify:due-reminders';

    protected $description = 'Remind students about assignments due in the next 24 hours';

    public function handle()
    {
        $assignments = Assignment::with('course')
            ->whereNull('reminded_at')
            ->where('due_at', '>', now())
            ->where('due_at', '<=', now()->addDay())
            ->get();

        $sent = 0;

        foreach ($assignments as $assignment) {
            $handedIn = $assignment->submissions()->pluck('student_id')->toArray();

            foreach ($assignment->course->students()->with('user')->get() as $student) {
                if (in_array($student->id, $handedIn) || !$student->user) {
                    continue;
                }

                Notifier::send($student->user, new CourseNotification('reminder', [
                    'title' => $assignment->title,
                    'due' => $assignment->due_at->translatedFormat('l g:i a'),
                ], $assignment->course->title, route('student.course', $assignment->course_id)));

                $sent++;
            }

            $assignment->reminded_at = now();
            $assignment->save();
        }

        $this->info($sent . ' reminders sent.');

        return 0;
    }
}
