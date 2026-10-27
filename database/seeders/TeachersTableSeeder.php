<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Schema;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeachersTableSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Teacher::truncate();
        Schema::enableForeignKeyConstraints();
        DB::table('teachers')->insert([
            [
                'name' =>"Teacher User",
                'image' => 'teacher_john.png',
                'major' => 'Physics',
                'categorie_id' => 1,
                'user_id' => 4
            ],
        ]);
    }
}
