<?php

namespace Database\Seeders;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class RemarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Project::all() as $project) {
            DB::table('remarks')->insert([
                'content' => 'remark_1',
                'user_id' => $project->domicile_establishment->scientific_committee_members()->get()->first()->user->id,
                'project_id' => $project->id,
                'created_at' => Carbon::now(),
                'updated_at' => null

            ]);
            DB::table('remarks')->insert([
                'content' => 'remark_2_',
                'user_id' => $project->domicile_establishment->scientific_committee_members()->get()->first()->user->id,
                'project_id' => $project->id,
                'created_at' => Carbon::now(),
                'updated_at' => null
            ]);
        }
    }
}
