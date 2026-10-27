<?php

namespace App\Http\Controllers\Teach;

use App\Http\Controllers\Controller as BaseController;
use App\Models\Course;
use App\Models\Role;

class Controller extends BaseController
{
    protected function myCourses()
    {
        $user = auth()->user();

        if ($user->hasRole(Role::ROLE_ADMIN)) {
            return Course::orderBy('title');
        }

        $teacherId = $user->teacher ? $user->teacher->id : 0;

        return Course::where('teacher_id', $teacherId)->orderBy('title');
    }

    protected function checkCourse(Course $course)
    {
        if (!$course->isTaughtBy(auth()->user())) {
            abort(403);
        }
    }
}
