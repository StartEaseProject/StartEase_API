<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //DB::transaction(function(){
            $this->call([
                RolePermissionSeeder::class,
                DataSeeder::class,
                PeriodSeeder::class,
                InternshipServiceMemberSeeder::class,
                ScientificCommitteeMemberSeeder::class,
                TeacherSeeder::class,
                StudentSeeder::class,
                ProjectSeeder::class,
                RemarkSeeder::class,
                CommentSeeder::class,
                TaskSeeder::class,
                DefenceSeeder::class
            ]);
        //});
    }
}
