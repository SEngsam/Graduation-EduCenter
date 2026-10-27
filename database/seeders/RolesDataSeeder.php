<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;
use App\Models\Ability;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;

class RolesDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    
    protected $ability = [
        'enroll_courses' => 'Enroll Courses',
        'view_reports' => 'View Reports',
        'all_roles' => 'All Roles',

        'all_users' => 'All Users',
        'add_user' => 'Add New User',
        'edit_user' => 'Edit User',
        'delete_user' => 'Delete User',

        'all_categories' => 'All Categories',
        'single_category' => 'Show Single Category',
        'add_category' => 'Add new category',
        'delete_category' => 'Delete Category',
        'edit_category' => 'Edit Category',

        'all_courses' => 'All courses',
        'single_course' => 'Show Single course',
        'add_course' => 'Add new course',
        'delete_course' => 'Delete course',
        'edit_course' => 'Edit course',
        'assign_teacher' => 'Assign Teacher',

        'all_teachers' => 'All Teachers',
        'single_teacher' => 'Show Single Teacher',
        'add_teacher' => 'Add new Teacher',
        'delete_teacher' => 'Delete Teacher',
        'edit_teacher' => 'Edit Teacher',
        
        'all_students' => 'ALl Students',

        'all_abouts' => 'All Abouts',
        'single_about' => 'Show Single About',
        'add_about' => 'Add new About',
        'delete_about' => 'Delete About',
        'edit_about' => 'Edit About',

        'all_events' => 'All Events',
        'single_event' => 'Show Single Event',
        'add_event' => 'Add new Event',
        'delete_event' => 'Delete Event',
        'edit_event' => 'Edit Event',

        'all_newss' => 'All Newss',
        'single_news' => 'Show Single News',
        'add_news' => 'Add new News',
        'delete_news' => 'Delete News',
        'edit_news' => 'Edit News',

    ];
    public function run()  //role
    {
        $roles = [
            ['name' => 'Super Admin'],
            ['name' => 'Category Manager'],
            ['name' => 'Course Manager'],
            ['name' => 'Teacher'],
            ['name' => 'Student'],
            ['name' => 'About Manager'],
            ['name' => 'Event Manager'],
            ['name' => 'News Manager'],

        ];
        Schema::disableForeignKeyConstraints();
        Ability::truncate();
        Role::truncate();
        Schema::enableForeignKeyConstraints();
        Role::insert($roles);

        foreach($this->ability as $code => $name) {
            Ability::create([
                'name' => $name,
                'code' => $code
            ]);
        }
    }
}
