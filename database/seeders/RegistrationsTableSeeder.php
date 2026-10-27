<?php

namespace Database\Seeders;

use App\Models\Registration;
use Illuminate\Database\Seeder;

class RegistrationsTableSeeder extends Seeder
{
    public function run()
    {
        Registration::query()->delete();

        Registration::create(['student_id' => 1, 'course_id' => 1]);
        Registration::create(['student_id' => 2, 'course_id' => 2]);
    }
}
