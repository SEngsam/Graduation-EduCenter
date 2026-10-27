<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Event;
use App\Models\Teacher;

class CatalogController extends Controller
{
    public function categories()
    {
        $categories = Category::orderBy('name_major')->get();

        $data = [];
        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->id,
                'name' => $category->name_major,
                'description' => $category->description,
                'image' => $category->photo(),
            ];
        }

        return response()->json(['data' => $data]);
    }

    public function courses()
    {
        $courses = Course::with('teacher')->withCount('students')->orderByDesc('id')->get();

        $data = [];
        foreach ($courses as $course) {
            $data[] = $this->courseData($course);
        }

        return response()->json(['data' => $data]);
    }

    public function course($id)
    {
        $course = Course::with(['teacher', 'slots'])->withCount('students')->find($id);

        if (!$course) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        $data = $this->courseData($course);
        $data['schedule'] = [];
        foreach ($course->slots as $slot) {
            $data['schedule'][] = [
                'day' => $slot->day,
                'starts_at' => substr($slot->starts_at, 0, 5),
                'ends_at' => substr($slot->ends_at, 0, 5),
                'room' => $slot->room,
            ];
        }

        return response()->json(['data' => $data]);
    }

    public function teachers()
    {
        $teachers = Teacher::with('category')->withCount('course')->orderBy('name')->get();

        $data = [];
        foreach ($teachers as $teacher) {
            $data[] = [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'major' => $teacher->major,
                'category' => $teacher->category->name_major,
                'courses_count' => $teacher->course_count,
                'image' => $teacher->photo(),
            ];
        }

        return response()->json(['data' => $data]);
    }

    public function events()
    {
        $events = Event::orderByDesc('id')->get();

        $data = [];
        foreach ($events as $event) {
            $data[] = [
                'id' => $event->id,
                'description' => $event->description,
                'location' => $event->location,
                'image' => $event->photo(),
            ];
        }

        return response()->json(['data' => $data]);
    }

    private function courseData($course)
    {
        return [
            'id' => $course->id,
            'title' => $course->title,
            'description' => $course->description,
            'teacher' => $course->teacher ? $course->teacher->name : null,
            'students_count' => $course->students_count,
            'image' => $course->photo(),
        ];
    }
}
