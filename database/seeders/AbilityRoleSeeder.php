<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbilityRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['ability_id' => 1, 'role_id' => 1],
            ['ability_id' => 2, 'role_id' => 1],
            ['ability_id' => 3, 'role_id' => 1],
            ['ability_id' => 4, 'role_id' => 1],
            ['ability_id' => 5, 'role_id' => 1],
            ['ability_id' => 6, 'role_id' => 1],
            ['ability_id' => 7, 'role_id' => 1],
            ['ability_id' => 8, 'role_id' => 1],
            ['ability_id' => 9, 'role_id' => 1],
            ['ability_id' => 10, 'role_id' => 1],
            ['ability_id' => 11, 'role_id' => 1],
            ['ability_id' => 12, 'role_id' => 1],
            ['ability_id' => 13, 'role_id' => 1],
            ['ability_id' => 14, 'role_id' => 1],
            ['ability_id' => 15, 'role_id' => 1],
            ['ability_id' => 16, 'role_id' => 1],
            ['ability_id' => 17, 'role_id' => 1],
            ['ability_id' => 18, 'role_id' => 1],
            ['ability_id' => 19, 'role_id' => 1],
            ['ability_id' => 20, 'role_id' => 1],
            ['ability_id' => 21, 'role_id' => 1],
            ['ability_id' => 22, 'role_id' => 1],
            ['ability_id' => 23, 'role_id' => 1],
            ['ability_id' => 24, 'role_id' => 1],
            ['ability_id' => 25, 'role_id' => 1],
            ['ability_id' => 26, 'role_id' => 1],
            ['ability_id' => 27, 'role_id' => 1],
            ['ability_id' => 28, 'role_id' => 1],
            ['ability_id' => 29, 'role_id' => 1],
            ['ability_id' => 30, 'role_id' => 1],
            ['ability_id' => 31, 'role_id' => 1],
            ['ability_id' => 32, 'role_id' => 1],
            ['ability_id' => 33, 'role_id' => 1],
            ['ability_id' => 34, 'role_id' => 1],
            ['ability_id' => 35, 'role_id' => 1],
            ['ability_id' => 36, 'role_id' => 1],
            ['ability_id' => 7, 'role_id' => 2],
            ['ability_id' => 8, 'role_id' => 2],
            ['ability_id' => 9, 'role_id' => 2],
            ['ability_id' => 10, 'role_id' => 2],
            ['ability_id' => 11, 'role_id' => 2],
            ['ability_id' => 12, 'role_id' => 3],
            ['ability_id' => 13, 'role_id' => 3],
            ['ability_id' => 14, 'role_id' => 3],
            ['ability_id' => 15, 'role_id' => 3],
            ['ability_id' => 16, 'role_id' => 3],
            ['ability_id' => 17, 'role_id' => 4],
            ['ability_id' => 18, 'role_id' => 4],
            ['ability_id' => 19, 'role_id' => 4],
            ['ability_id' => 20, 'role_id' => 4],
            ['ability_id' => 21, 'role_id' => 4],
            ['ability_id' => 1, 'role_id' => 5],
            ['ability_id' => 23, 'role_id' => 6],
            ['ability_id' => 24, 'role_id' => 6],
            ['ability_id' => 25, 'role_id' => 6],
            ['ability_id' => 26, 'role_id' => 6],
            ['ability_id' => 27, 'role_id' => 7],
            ['ability_id' => 28, 'role_id' => 7],
            ['ability_id' => 29, 'role_id' => 7],
            ['ability_id' => 30, 'role_id' => 7],
            ['ability_id' => 31, 'role_id' => 7],
            ['ability_id' => 32, 'role_id' => 8],
            ['ability_id' => 33, 'role_id' => 8],
            ['ability_id' => 34, 'role_id' => 8],
            ['ability_id' => 35, 'role_id' => 8],
            ['ability_id' => 36, 'role_id' => 8],
            ['ability_id' => 37, 'role_id' => 8],
            ['ability_id' => 38, 'role_id' => 8],
        ];
        Schema::disableForeignKeyConstraints();
        DB::table('ability_role')->truncate();

        Schema::enableForeignKeyConstraints();
        DB::table('ability_role')->insert($data);
    }
}
