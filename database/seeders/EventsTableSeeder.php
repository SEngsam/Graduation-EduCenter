<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Schema;
use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventsTableSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Event::truncate();
        Schema::enableForeignKeyConstraints();
        DB::table('events')->insert([
            [
                'image' => 'science_fair.png',
                'location' => 'Hall A',
                'description' => 'Join the annual science fair.',
            ],
            [
                'image' => 'art_exhibition.png',
                'location' => 'Gallery 2',
                'description' => 'Explore the latest art exhibition.',
            ],
        ]);
    }
}
