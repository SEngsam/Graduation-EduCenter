<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Schema;
use App\Models\About;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AboutsTableSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        About::truncate();
        Schema::enableForeignKeyConstraints();
        DB::table('abouts')->insert([
            [
                'image' => 'about_us.png',
                'title' => 'Who We Are',
                'description' => 'An educational platform for everyone.',
                'btn_text' => 'Learn More',
                'btn_link' => '/about',
            ],
        ]);
    }
}
