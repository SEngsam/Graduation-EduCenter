<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Schema;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Category::truncate();
        Schema::enableForeignKeyConstraints();
        DB::table('categories')->insert([
            [
                'name_major' => 'Science',
                'image' => 'science.png',
                'title' => 'Explore Science',
                'description' => 'Dive into the wonders of scientific discovery.',
            ],
            [
                'name_major' => 'Arts',
                'image' => 'arts.png',
                'title' => 'Creative Arts',
                'description' => 'Unleash your artistic potential and creativity.',
            ],
            [
                'name_major' => 'Technology',
                'image' => 'technology.png',
                'title' => 'Tech Innovations',
                'description' => 'Stay ahead with the latest in technology.',
            ],
        ]);
    }
}

