<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificate extends Model
{
    const MIN_ATTENDANCE = 75;
    const MIN_GRADE = 12;

    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public static function newCode()
    {
        do {
            $code = 'EC-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public static function progressFor(Student $student, Course $course)
    {
        $lessonIds = $course->lessons()->where('is_published', true)->pluck('id');
        $lessonsDone = $student->completedLessons()->whereIn('lessons.id', $lessonIds)->count();

        $sessionIds = $course->sessions()->pluck('id');
        $statuses = $student->attendances()->whereIn('class_session_id', $sessionIds)->pluck('status');
        $attendance = null;
        if (count($statuses) > 0) {
            $came = 0;
            foreach ($statuses as $status) {
                if ($status == 'present' || $status == 'late') {
                    $came++;
                }
            }
            $attendance = (int) round($came * 100 / count($statuses));
        }

        $assignmentIds = $course->assignments()->pluck('id');
        $graded = $student->submissions()->with('assignment')
            ->whereIn('assignment_id', $assignmentIds)
            ->whereNotNull('grade')
            ->get();
        $grade = null;
        if (count($graded) > 0) {
            $sum = 0;
            foreach ($graded as $submission) {
                $sum += $submission->grade * 20 / $submission->assignment->max_grade;
            }
            $grade = round($sum / count($graded), 1);
        }

        $lessonsOk = count($lessonIds) > 0 && $lessonsDone == count($lessonIds);
        $attendanceOk = $attendance === null || $attendance >= self::MIN_ATTENDANCE;
        $gradeOk = count($assignmentIds) == 0 || ($grade !== null && $grade >= self::MIN_GRADE);

        return [
            'lessons_done' => $lessonsDone,
            'lessons_total' => count($lessonIds),
            'lessons_ok' => $lessonsOk,
            'attendance' => $attendance,
            'attendance_ok' => $attendanceOk,
            'grade' => $grade,
            'grade_ok' => $gradeOk,
            'eligible' => $lessonsOk && $attendanceOk && $gradeOk,
        ];
    }
}
