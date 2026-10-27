<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Registration;
use App\Models\Submission;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('ability:view_reports');
    }

    public function index(Request $request)
    {
        return view('admin.reports.index', $this->buildReport($request));
    }

    public function print(Request $request)
    {
        return view('admin.reports.print', $this->buildReport($request));
    }

    public function export(Request $request)
    {
        $report = $this->buildReport($request);

        $rows = [];
        $rows[] = [__('dash.course'), __('dash.teacher'), __('dash.students'), __('dash.avg_grade'), __('dash.attendance_rate')];
        foreach ($report['courses'] as $row) {
            $rows[] = [
                $row['title'],
                $row['teacher'],
                $row['students'],
                $row['grade'] === null ? '' : $row['grade'],
                $row['attendance'] === null ? '' : $row['attendance'] . '%',
            ];
        }

        $csv = "\xEF\xBB\xBF";
        foreach ($rows as $row) {
            $cells = [];
            foreach ($row as $cell) {
                $cells[] = '"' . str_replace('"', '""', $cell) . '"';
            }
            $csv .= implode(',', $cells) . "\r\n";
        }

        $fileName = 'educenter-report-' . now()->format('Y-m-d') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    private function buildReport(Request $request)
    {
        $months = (int) $request->input('months', 6);
        if (!in_array($months, [3, 6, 12])) {
            $months = 6;
        }

        $from = now()->startOfMonth()->subMonths($months - 1);
        $to = now();
        $previousFrom = $from->copy()->subMonths($months);

        $enrollments = $this->enrollmentsByMonth($from, $months);
        $total = array_sum(array_column($enrollments, 'count'));
        $previousTotal = Registration::whereBetween('created_at', [$previousFrom, $from])->count();

        $attendance = $this->attendanceRate($from, $to);
        $previousAttendance = $this->attendanceRate($previousFrom, $from);

        return [
            'months' => $months,
            'from' => $from,
            'enrollments' => $enrollments,
            'stats' => [
                'enrollments' => $total,
                'enrollments_change' => $this->change($total, $previousTotal),
                'attendance' => $attendance,
                'attendance_change' => $attendance !== null && $previousAttendance !== null ? $attendance - $previousAttendance : null,
                'submissions' => $this->submissionRate($from, $to),
                'grade' => $this->averageGrade($from, $to, null),
            ],
            'courses' => $this->courseRows($from, $to),
        ];
    }

    private function enrollmentsByMonth($from, $months)
    {
        $dates = Registration::where('created_at', '>=', $from)->pluck('created_at');

        $result = [];
        for ($i = 0; $i < $months; $i++) {
            $month = $from->copy()->addMonths($i);
            $result[$month->format('Y-m')] = [
                'label' => $month->translatedFormat('M'),
                'count' => 0,
            ];
        }

        foreach ($dates as $date) {
            $key = Carbon::parse($date)->format('Y-m');
            if (isset($result[$key])) {
                $result[$key]['count']++;
            }
        }

        return array_values($result);
    }

    private function attendanceRate($from, $to, $courseId = null)
    {
        $statuses = Attendance::whereHas('session', function ($query) use ($from, $to, $courseId) {
            $query->whereBetween('starts_at', [$from, $to]);
            if ($courseId) {
                $query->where('course_id', $courseId);
            }
        })->pluck('status');

        if (count($statuses) == 0) {
            return null;
        }

        $came = 0;
        foreach ($statuses as $status) {
            if ($status == 'present' || $status == 'late') {
                $came++;
            }
        }

        return (int) round($came * 100 / count($statuses));
    }

    private function submissionRate($from, $to)
    {
        $assignments = Assignment::with('course')->withCount('submissions')
            ->whereBetween('due_at', [$from, $to])
            ->get();

        $expected = 0;
        $handedIn = 0;
        foreach ($assignments as $assignment) {
            $expected += $assignment->course->students()->count();
            $handedIn += $assignment->submissions_count;
        }

        if ($expected == 0) {
            return null;
        }

        return (int) round(min($handedIn, $expected) * 100 / $expected);
    }

    private function averageGrade($from, $to, $courseId)
    {
        $submissions = Submission::with('assignment')
            ->whereNotNull('grade')
            ->whereBetween('graded_at', [$from, $to])
            ->whereHas('assignment', function ($query) use ($courseId) {
                if ($courseId) {
                    $query->where('course_id', $courseId);
                }
            })
            ->get();

        if (count($submissions) == 0) {
            return null;
        }

        $sum = 0;
        foreach ($submissions as $submission) {
            $sum += $submission->grade * 20 / $submission->assignment->max_grade;
        }

        return round($sum / count($submissions), 1);
    }

    private function courseRows($from, $to)
    {
        $rows = [];
        foreach (Course::with('teacher')->withCount('students')->get() as $course) {
            $rows[] = [
                'id' => $course->id,
                'title' => $course->title,
                'teacher' => $course->teacher ? $course->teacher->name : '—',
                'students' => $course->students_count,
                'grade' => $this->averageGrade($from, $to, $course->id),
                'attendance' => $this->attendanceRate($from, $to, $course->id),
            ];
        }

        usort($rows, function ($a, $b) {
            return ($a['attendance'] ?? 101) <=> ($b['attendance'] ?? 101);
        });

        return $rows;
    }

    private function change($now, $before)
    {
        if ($before == 0) {
            return null;
        }

        return (int) round(($now - $before) * 100 / $before);
    }
}
