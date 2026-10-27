<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Schema;
use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoursesTableSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Course::truncate();
        Schema::enableForeignKeyConstraints();
        DB::table('courses')->insert([
            [
                'image' => 'physics_course.png',
                'title' => 'Introduction to Physics',
                'description' => 'Learn the basics of physics.',
                'btn_text' => 'Enroll Now',
                'btn_link' => '/courses/1',
            ],
            [
                'image' => 'painting_course.png',
                'title' => 'Mastering Painting',
                'description' => 'Learn advanced painting techniques.',
                'btn_text' => 'Enroll Now',
                'btn_link' => '/courses/2',
            ],
        ]);
    }
}

