<?php

namespace Tests;

use App\Models\Category;
use App\Models\Course;
use App\Models\Registration;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    protected function makeRoles()
    {
        foreach ([1 => 'Super Admin', 4 => 'Teacher', 5 => 'Student'] as $id => $name) {
            $role = new Role();
            $role->id = $id;
            $role->name = $name;
            $role->save();
        }
    }

    protected function makeUser($roleId, $name = 'Test User')
    {
        return User::create([
            'name' => $name,
            'email' => uniqid() . '@example.com',
            'password' => Hash::make('password'),
            'role_id' => $roleId,
        ]);
    }

    protected function makeStudent($name = 'طالب')
    {
        $user = $this->makeUser(Role::ROLE_STUDENT, $name);

        Student::create([
            'name' => $name,
            'major' => 'علوم',
            'no_phone' => uniqid(),
            'user_id' => $user->id,
        ]);

        return $user->fresh();
    }

    protected function makeTeacher($name = 'مدرّس')
    {
        $category = Category::first();
        if (!$category) {
            $category = Category::create(['name_major' => 'Science', 'image' => 'x.jpg', 'title' => 'Science', 'description' => 'Science']);
        }

        $user = $this->makeUser(Role::ROLE_TEACHER, $name);

        Teacher::create([
            'categorie_id' => $category->id,
            'user_id' => $user->id,
            'image' => 'x.jpg',
            'name' => $name,
            'major' => 'Physics',
        ]);

        return $user->fresh();
    }

    protected function makeCourse($teacherUser = null, $title = 'Physics')
    {
        return Course::create([
            'image' => 'x.jpg',
            'title' => $title,
            'description' => 'desc',
            'btn_text' => 'more',
            'btn_link' => '#',
            'teacher_id' => $teacherUser ? $teacherUser->teacher->id : null,
        ]);
    }

    protected function enroll($studentUser, $course)
    {
        Registration::create([
            'student_id' => $studentUser->student->id,
            'course_id' => $course->id,
        ]);
    }
}
