<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class StudentsTableSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Student::truncate();
        Schema::enableForeignKeyConstraints();

        $students = [
            ['سارة أحمد', 'student@example.com', 'علوم', '0991234567'],
            ['محمد علي', 'mohamed@example.com', 'فنون', '0997654321'],
        ];

        foreach ($students as $row) {
            $user = User::create([
                'name' => $row[0],
                'email' => $row[1],
                'password' => Hash::make('password'),
                'role_id' => Role::ROLE_STUDENT,
            ]);

            Student::create([
                'name' => $row[0],
                'major' => $row[2],
                'no_phone' => $row[3],
                'user_id' => $user->id,
            ]);
        }
    }
}
