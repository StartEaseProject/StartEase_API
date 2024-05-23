<?php

namespace Database\Seeders;

use App\Models\Defence;
use App\Models\Jury;
use App\Models\MemberDeliberation;
use App\Models\Project;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function(){
            $bool = false;
            $open = true;
            $on_site = true;
            $room = true;
            $delib = false;
            $app = ['good', 'bad', 'medium'];
            foreach (Project::all() as $project) {
                if ($bool) {
                    $project->update([
                        'is_authorized_defence' => true
                    ]);
                    $d = Defence::create([
                        'date' => Carbon::today()->addDays(5)->format('Y-m-d'),
                        'time' => Carbon::now()->format('H:i'),
                        'establishment_id' => $project->establishment_id,
                        'room_id' => $room ? $project->domicile_establishment->rooms->first()->id : null,
                        'other_place' => $room ? null : 'some place',
                        'mode' => $on_site ? Defence::MODES['ON_SITE'] : Defence::MODES['REMOTE'],
                        'nature' => $open ? Defence::NATURES['OPEN'] : Defence::NATURES['CLOSED'],
                        'reserves' => null,
                        'files' => (object) [],
                        'updated_at' => null,
                    ]);
                    $project->update(['defence_id' => $d->id]);
                    $teachers = $project->domicile_establishment->teachers->map(function ($teach) {
                        return $teach->user->id;
                    })->toArray();
                    if($project->co_supervisor_id){
                        $teachers_not_in_project = User::whereIn('id', $teachers)
                            ->whereNotIn('id', [$project->supervisor_id, $project->co_supervisor_id])->get();
                    } else{
                        $teachers_not_in_project = User::whereIn('id', $teachers)
                        ->whereNotIn('id', [$project->supervisor_id])->get();
                    }
                    $jurys = [
                        [
                            'jury_id' => $teachers_not_in_project->first()->id,
                            'defence_id' => $d->id,
                            'role' => Defence::JURY_ROLES['PRESIDENT']
                        ],
                        [
                            'jury_id' => $project->supervisor_id,
                            'defence_id' => $d->id,
                            'role' => Defence::JURY_ROLES['SUPERVISOR']
                        ],
                        [
                            'jury_id' => $teachers_not_in_project->skip(1)->take(1)->first()->id,
                            'defence_id' => $d->id,
                            'role' => Defence::JURY_ROLES['EXAMINER']
                        ],
                        [
                            'jury_id' => $teachers_not_in_project->skip(2)->take(1)->first()->id,
                            'defence_id' => $d->id,
                            'role' => Defence::JURY_ROLES['EXAMINER']
                        ],
                        [
                            'jury_id' => $teachers_not_in_project->skip(3)->take(1)->first()->id,
                            'defence_id' => $d->id,
                            'role' => Defence::JURY_ROLES['EXAMINER']
                        ],
                    ];

                    if ($project->co_supervisor_id) {
                        array_push($jurys, [
                            'jury_id' => $project->co_supervisor_id,
                            'defence_id' => $d->id,
                            'role' => Defence::JURY_ROLES['CO_SUPERVISOR']
                        ]);
                    }
                    $temp = Role::firstWhere('name', Role::DEFAULT_ROLES['JURY'])->id;
                    foreach ($jurys as $jury) {
                        Jury::create($jury);
                        $user = User::find($jury['jury_id']);
                        $user->roles()->syncWithoutDetaching([$temp]);
                    }
                    foreach ($project->members as $member) {
                        $p = $app[array_rand($app)];
                        MemberDeliberation::create([
                            'member_id' => $member->id,
                            'defence_id' => $d->id,
                            'mark' => rand(10, 20),
                            'mention' => $p,
                            'diploma_url' => '',
                            'appreciation' => 'it was ' . $p
                        ]);
                    }

                    $open = !$open;
                    $on_site = !$on_site;
                    $room = !$room;
                    $delib = !$delib;
                }
                $bool = !$bool;
            }
        });
    }
}
