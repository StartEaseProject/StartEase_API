<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bool = false;
        foreach (Project::all() as $project) {
            if($bool){
                for ($i = 0; $i < 5; $i++) {
                    Task::create([
                        'title' => "title_" . $i,
                        'description' => "description_" . $i,
                        'deadline' => Carbon::today()->addDays($i)->format('Y-m-d'),
                        'status' => Task::STATUSES['IN_PROGRESS'],
                        'resources' => [
                            'res1' => null,
                            'res2' => null,
                            'res3' => null
                        ],
                        'project_id' => $project->id,
                        'updated_at' => null,
                        'created_at' => Carbon::now()
                    ]);
                }
            }
            $bool = !$bool;
        }
    }
}
