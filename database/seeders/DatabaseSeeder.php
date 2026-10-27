<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RolesDataSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $this->call([
            RolesDataSeeder::class,
            AbilityRoleSeeder::class,
            UsersTableSeeder::class,
            CategoriesTableSeeder::class,
            TeachersTableSeeder::class,
            StudentsTableSeeder::class,
            CoursesTableSeeder::class,
            MajorsTableSeeder::class,
            EventsTableSeeder::class,
            AboutsTableSeeder::class,
            RegistrationsTableSeeder::class,
            NewsTableSeeder::class,
            AcademicSeeder::class,
          
        ]);
    }
}
