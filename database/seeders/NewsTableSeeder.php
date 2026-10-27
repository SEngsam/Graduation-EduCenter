<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Schema;
use App\Models\News;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewsTableSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        News::truncate();
        Schema::enableForeignKeyConstraints();
        DB::table('news')->insert([
            [
                'image' => 'news1.png',
                'date' => '2024-11-20',
                'auther' => 'Admin',
                'title' => 'New Courses Available!',
                'description' => 'We have added exciting new courses.',
                'btn_text' => 'Check Now',
                'btn_link' => '/news/1',
            ],
        ]);
    }
}
