<?php

namespace Database\Seeders;

use App\Models\Establishment;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Establishment::all() as $estb) {
            DB::table('user_role')->insert([
                'role_id' => Role::firstWhere('name', 'project_holder')->id,
                'user_id' => $estb->students()->get()->first()->user->id
            ]);
            DB::table('user_role')->insert([
                'role_id' => Role::firstWhere('name', 'supervisor')->id,
                'user_id' => $estb->teachers()->get()->first()->user->id
            ]);
            DB::table('user_role')->insert([
                'role_id' => Role::firstWhere('name', 'supervisor')->id,
                'user_id' => $estb->teachers()->get()->skip(1)->take(1)->first()->user->id
            ]);
            $proj = Project::create([
                'type' => Project::TYPES['STARTUP'],
                'trademark_name' => 'trademark_1_'.$estb->id,
                'scientific_name' => 'scientific_1_' . $estb->id,
                'resume' => 'resume_1_' . $estb->id,
                'establishment_id' => $estb->id,
                'project_holder_id' => $estb->students()->get()->first()->user->id,
                'supervisor_id' => $estb->teachers()->get()->first()->user->id,
                'co_supervisor_id' => $estb->teachers()->get()->skip(1)->take(1)->first()->user->id,
                'submission_date' => Carbon::today(),
                'updated_at' => null,
                'progress' => (object) [],
                'files' => (object) [],
            ]);
            ProjectMember::create([
                'member_id' => $estb->students()->get()->first()->user->id,
                'project_id' => $proj->id
            ]);
            DB::table('user_role')->insert([
                'role_id' => Role::firstWhere('name', 'project_member')->id,
                'user_id' => $estb->students()->get()->first()->user->id
            ]);



            DB::table('user_role')->insert([
                'role_id' => Role::firstWhere('name', 'project_holder')->id,
                'user_id' => $estb->teachers()->get()->first()->user->id
            ]);
            $proj2 = Project::create([
                'type' => Project::TYPES['STARTUP'],
                'trademark_name' => 'trademark_2_' . $estb->id,
                'scientific_name' => 'scientific_2_' . $estb->id,
                'resume' => 'resume_2_' . $estb->id,
                'establishment_id' => $estb->id,
                'project_holder_id' => $estb->teachers()->get()->first()->user->id,
                'supervisor_id' => $estb->teachers()->get()->first()->user->id,
                'co_supervisor_id' => $estb->teachers()->get()->skip(1)->take(1)->first()->user->id,
                'submission_date' => Carbon::today(),
                'updated_at' => null,
                'progress' => (object) [],
                'files' => (object) [],
            ]);
            foreach ($estb->students->skip(1)->take(2) as $student) {
                ProjectMember::create([
                    'member_id' => $student->user->id,
                    'project_id' => $proj2->id
                ]);
                DB::table('user_role')->insert([
                    'role_id' => Role::firstWhere('name', 'project_member')->id,
                    'user_id' => $student->user->id
                ]);
            }


            $proj3 = Project::create([
                'type' => Project::TYPES['BREVET'],
                'trademark_name' => 'trademark_3_' . $estb->id,
                'scientific_name' => 'scientific_3_' . $estb->id,
                'resume' => 'resume_3_' . $estb->id,
                'establishment_id' => $estb->id,
                'project_holder_id' => $estb->teachers()->get()->first()->user->id,
                'supervisor_id' => $estb->teachers()->get()->first()->user->id,
                'submission_date' => Carbon::today(),
                'updated_at' => null,
                'progress' => (object) [],
                'files' => (object) [],
            ]);
            foreach ($estb->students->skip(3)->take(2) as $student) {
                ProjectMember::create([
                    'member_id' => $student->user->id,
                    'project_id' => $proj3->id
                ]);
                DB::table('user_role')->insert([
                    'role_id' => Role::firstWhere('name', 'project_member')->id,
                    'user_id' => $student->user->id
                ]);
            }
        }
    }
}