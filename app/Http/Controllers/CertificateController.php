<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function show(Course $course)
    {
        $student = auth()->user()->student;

        if (!$student || !$course->students()->where('students.id', $student->id)->exists()) {
            abort(403);
        }

        $certificate = Certificate::where('student_id', $student->id)->where('course_id', $course->id)->first();

        if (!$certificate) {
            $progress = Certificate::progressFor($student, $course);

            if (!$progress['eligible']) {
                return redirect()->route('student.course', $course->id)->with('error', __('cert.not_yet'));
            }

            $certificate = Certificate::create([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'code' => Certificate::newCode(),
                'grade' => $progress['grade'],
                'attendance' => $progress['attendance'],
            ]);
        }

        return view('certificates.show', compact('certificate'));
    }

    public function verify(Request $request, $code = null)
    {
        $code = strtoupper(trim($code ?? $request->input('code', '')));
        $certificate = null;

        if ($code) {
            $certificate = Certificate::with(['student', 'course.teacher'])->where('code', $code)->first();
        }

        return view('certificates.verify', compact('code', 'certificate'));
    }
}
