<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Schema;
use App\Models\Major;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MajorsTableSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Major::truncate();
        Schema::enableForeignKeyConstraints();
        DB::table('majors')->insert([
            ['id' => 1],
            ['id' => 2],
        ]);
    }
}
