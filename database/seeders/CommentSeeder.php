<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bool = false;
        foreach (Project::all() as $project) {
            if($bool){
                $project->update([
                    'decision_date' => Carbon::now(),
                    'status' => Project::STATUSES['ACCEPTED'],
                ]);
                $parent = Comment::create([
                    'content' => 'comment_parent_1',
                    'user_id' => $project->supervisor_id,
                    'project_id' => $project->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => null
                ]);
                Comment::create([
                    'parent_comment_id' => $parent->id,
                    'content' => 'child_1',
                    'user_id' => $project->members()->get()->first()->id,
                    'project_id' => $project->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => null
                ]);
                Comment::create([
                    'parent_comment_id' => $parent->id,
                    'content' => 'child_2',
                    'user_id' => $project->members()->get()->first()->id,
                    'project_id' => $project->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => null
                ]);
                Comment::create([
                    'parent_comment_id' => $parent->id,
                    'content' => 'comment_child_3',
                    'user_id' => $project->members()->get()->first()->id,
                    'project_id' => $project->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => null
                ]);
                Comment::create([
                    'content' => 'comment_parent_3',
                    'user_id' => $project->supervisor_id,
                    'project_id' => $project->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => null
                ]);
            }
            $bool = !$bool;
        }
    }
}
