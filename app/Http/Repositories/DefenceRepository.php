<?php

namespace App\Http\Repositories;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Period;
use App\Models\Defence;

use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Interfaces\DefenceInterface;
use App\Notifications\ConfirmRegisterNotification;
use App\Http\Requests\Defence\CreateDefenseRequset;
use App\Http\Requests\Defence\UpdateDefenceRequest;
use App\Notifications\DefenceInvitationNotification;
use App\Http\Requests\Defence\UploadThesisDefFilesRequest;
use App\Models\Jury;
use App\Models\Room;
use App\Models\Scientific_committee_member;
use App\Models\Internship_service_member;
use App\Models\Headmaster;
use App\Models\Student;
use App\Models\Teacher;
use Exception;
use Illuminate\Http\Request;

class DefenceRepository implements DefenceInterface
{
    public function __construct(
        private Defence $defence,
        private Period $period,
        private Role $role,
        private Room $room,
        private Project $project,
        private User $user,
        private TeacherRepository $teacherRepository,
        private Jury $jury
    ) {}


    private function validate_request(Request $request): ?String
    {
        if (!$request->room_id && !$request->other_place)
            return 'please provide at least one of the room or other place';
        if ($request->room_id && $request->other_place) 
            return 'please choose one of the room or other place';
        if (in_array($request->president, $request->examiners))
            return 'president must be different then examiners';
        
        return null;
    }  
    private function is_jury_free(User $jury, $startTime, $day): bool
    {
        return !$jury->defences()
            ->whereDate('date', $day)
            ->whereBetween('time', [$startTime, Carbon::parse($startTime)->addHours($this->defence::DURATION)])
            ->exists();
    }
    private function is_the_room_free(Room $room, $startTime, $day): bool
    {
        return !$room->defences()
            ->whereDate('date', $day)
            ->whereBetween('time', [$startTime, Carbon::parse($startTime)->addHours($this->defence::DURATION)])
            ->exists();
    }
    private function validate_president(User $president, $startTime, $day, $estblishment_id): array
    {
        if ($president->person_type !== Teacher::class)
            return [
                "success" => false,
                "message" => "the president you provided isn't a teacher"
            ];
        if ($president->person->establishment_id !== $estblishment_id)
            return [
                "success" => false,
                "message" => "this jury isn't from your establishment"
            ];
        if (!$this->is_jury_free($president, $startTime, $day)) {
            return [
                'success' => false,
                'message' => 'The president already has a defence during the specified day and time',
            ];
        }
        return [
            'success' => true,
        ];
    }
    private function validate_examiner(User $examiner, $startTime, $day): array
    {
        if ($examiner->person_type !== Teacher::class)
            return [
                "success" => false,
                "message" => "the examiner ".$examiner->email." isn't a teacher"
            ];
        if (!$this->is_jury_free($examiner, $startTime, $day)) {
            return [
                'success' => false,
                'message' => 'the examiner '.$examiner->email.' already has a defence during the specified day and time',
            ];
        }
        return [
            'success' => true,
        ];
    }


    public function all(): array
    {
        $auth = auth()->user();
        $defences = [];
        switch ($auth->person_type){
            case Teacher::class:
                $defences = $auth->defences;
                break;
            case Scientific_committee_member::class:
            case Internship_service_member::class:
            case Headmaster::class:
                $defences = $auth->person->establishment->defences;
                break;
        }
        return [
            'success' => true,
            'message' => 'defences retreived successfully',
            'defences' => $defences
        ];
    }

    public function getById($id): array
    {
        try {
            $defence = $this->defence::find($id);
            if (!$defence) {
                return [
                    'success' => false,
                    'message' => 'Defense not found',
                ];
            }
            $auth = auth()->user();
            if (!$defence->allow_view($auth)) {
                return [
                    'success' => false,
                    'message' => 'Not allowed',
                ];
            }
            return [
                'success' => true,
                'message' => 'defence retreived successfully',
                'defence' => $defence
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Something went wrong, please try again",
            ];
        }
    }

    public function create_defence(CreateDefenseRequset $request, $project_id): array
    {
        $error = $this->validate_request($request);
        if($error)
            return [
                'success' => false,
                'message' => $error,
            ];
        try {
            $project = $this->project::find($project_id);
            if (!$project) {
                return [
                    'success' => false,
                    'message' => 'Project not found',
                ];
            }
            $user_auth = auth()->user();
            if(!$project->same_establishment($user_auth)){
                return [
                    'success' => false,
                    'message' => 'Not allowed to create defence for this project',
                ];
            }
            if ($project->defence_id) {
                return [
                    'success' => false,
                    'message' => 'This project already has a defence',
                ];
            }
            if (!$project->is_authorized_defence) {
                return [
                    'success' => false,
                    'message' => 'This project is not authorized yet',
                ];
            }
            if($request->room_id){
                $room = $user_auth->person->establishment->rooms()->find($request->room_id);
                if (!$room) {
                    return [
                        'success' => false,
                        'message' => 'Room not found in your establishment',
                    ];
                }
                if (!$this->is_the_room_free($room, $request->time, $request->date)) {
                    return [
                        'success' => false,
                        'message' => 'The room is not available during the specified day and time',
                    ];
                }
            }
            $examiners = $request->examiners;
            $examiners_not_registered = [];
            $president_is_new = false;
            $jurys = [[
                'jury' => $project->supervisor,
                'role' =>$this->defence::JURY_ROLES['SUPERVISOR']
            ]];

            if($project->co_supervisor){
                array_push($jurys, [
                    'jury' => $project->co_supervisor,
                    'role' => $this->defence::JURY_ROLES['CO_SUPERVISOR'] 
                ]);
            }
            $president = $this->user::firstWhere('email', $request->president);
            if (!$president) $president_is_new = true;
            else {
                $res = $this->validate_president($president, $request->time, $request->date, $project->establishment_id);
                if (!$res['success']) return $res;
                array_push($jurys, ['jury' => $president, 'role' => $this->defence::JURY_ROLES['PRESIDENT']]);
            }

            foreach ($examiners as $email) {
                $examiner = $this->user::firstWhere('email', $email);
                if ($examiner) {
                    $res = $this->validate_examiner($examiner, $request->time, $request->date);
                    if (!$res['success']) return $res;
                    array_push($jurys, ['jury' => $examiner, 'role' => $this->defence::JURY_ROLES['EXAMINER']]);
                } else array_push($examiners_not_registered, $email);
            }

            /** @var Defence */
            $defence = null;
            DB::transaction(function () use (
                $user_auth,
                &$president,
                &$jurys,
                &$examiners_not_registered,
                $request,
                &$defence,
                &$project,
                $president_is_new,
            ) {
                if ($president_is_new){
                    $president = $this->teacherRepository->create_jury($request->president);
                    array_push($jurys, ['jury' => $president, 'role' => $this->defence::JURY_ROLES['PRESIDENT']]);
                }               

                $defence = $this->defence::create([
                    'date' => $request->date,
                    'time' => $request->time,
                    'establishment_id' => $user_auth->person->establishment_id,
                    'room_id' => $request->room_id ?? null,
                    'other_place' => $request->other_place ?? null,
                    'mode' => $request->mode,
                    'nature' => $request->nature,
                    'files' => (object) [],
                    'updated_at' => null,
                    'guest' => $request->guest
                ]);
                $project->update([
                    'defence_id' => $defence->id,
                ]);

                foreach ($examiners_not_registered as $email) {
                    $new = $this->teacherRepository->create_jury($email);
                    array_push($jurys, ['jury' => $new, 'role' => $this->defence::JURY_ROLES['EXAMINER']]);
                }
                
                $temp = $this->role::firstWhere('name', $this->role::DEFAULT_ROLES["JURY"]);
                foreach ($jurys as $jury) {
                    $jury['jury']->roles()->syncWithoutDetaching([$temp->id]);
                    $this->jury::create([
                        "jury_id" => $jury['jury']->id,
                        "defence_id" => $defence->id,
                        "role" => $jury['role'],
                    ]);
                }
                $defence->refresh();
            });

            foreach ($jurys as $jury) {
                $jury['jury']->notify(new DefenceInvitationNotification($jury['role'], $defence, Teacher::class));
                if ($jury['jury']->register_verification_hash)
                    $jury['jury']->notify(new ConfirmRegisterNotification($jury['jury']->id, $jury['jury']->register_verification_hash));
            }
            foreach ($project->members as $member) {
                $member->notify(new DefenceInvitationNotification("member", $defence, Student::class));
            }
            return [
                'success' => true,
                'message' =>  "Defence created successfully. An email was sent to all jury members.",
                'defence' => $defence
            ];
        } catch (\Exception $e) {
            throw $e;
            return [
                "success" => false,
                "message" => "something went wrong, please try again",
            ];
        }
    }

    public function update_defence(UpdateDefenceRequest $request, $id): array
    {
        $error = $this->validate_request($request);
        if ($error)
            return [
                'success' => false,
                'message' => $error,
            ];
        $defence = $this->defence::find($id);
        if (!$defence) {
            return [
                'success' => false,
                'message' => 'defence not found',
            ];
        }
        $user_auth = auth()->user();
        if (!$defence->same_establishment($user_auth)) {
            return [
                'success' => false,
                'message' => 'Not allowed to update defence for this project',
            ];
        }
        if ($request->room_id) {
            $room = $user_auth->person->establishment->rooms()->find($request->room_id);
            if (!$room) {
                return [
                    'success' => false,
                    'message' => 'Room not found in your establishment',
                ];
            }
            if($defence->time!== $request->time && $defence->date!== $request->date && $defence->room_id === $request->room_id){
                if (!$this->is_the_room_free($room, $request->time, $request->date)) {
                    return [
                        'success' => false,
                        'message' => 'The room is not available during the specified day and time',
                    ];
                }
            }
            if (!$this->is_the_room_free($room, $request->time, $request->date)) {
                return [
                    'success' => false,
                    'message' => 'The room is not available during the specified day and time',
                ];
            }
        }

        $new_jurys = [];
        $current_jurys = $defence->jurys_formatted();
        $examiners = $request->examiners;
        $examiners_not_registered = [];
        $examiners_new = array_diff($examiners, array_column($current_jurys['examiners'], 'email'));
        $examiners_removed = array_filter($current_jurys['examiners'], function ($examiner) use ($examiners) {
            return !in_array($examiner['email'], $examiners);
        });
        $president_is_new = false;
        $president_is_new_to_defence = false;

        if ($current_jurys['president']['email'] !== $request->president) {
            $president_is_new_to_defence = true;
            $president = $this->user->firstWhere('email', $request->president);
            if ($president){
                $res = $this->validate_president($president, $request->time, $request->date, $defence->establishment_id);
                if (!$res['success']) return $res;
                array_push($new_jurys, [
                    'jury' => $president,
                    'role' => $this->defence::JURY_ROLES['PRESIDENT']
                ]);
            }
            else {
                $president_is_new = true;
            }
        }

        foreach ($examiners_new as $email) {
            $examiner = $this->user::firstWhere('email', $email);
            if ($examiner) {
                $res = $this->validate_examiner($examiner, $request->time, $request->date);
                if (!$res['success']) return $res;
                array_push($new_jurys, [
                    'jury' => $examiner,
                    'role' => $this->defence::JURY_ROLES['EXAMINER']
                ]);
            } else
                array_push($examiners_not_registered, $email);
        }

        DB::transaction(function () use (
            $president_is_new_to_defence,
            $president_is_new,
            &$new_jurys,
            &$defence,
            &$president,
            $examiners_not_registered,
            $request,
            $examiners_removed,
            $current_jurys
        ) {
            if ($president_is_new){
                $president = $this->teacherRepository->create_jury($request->president);
                array_push($new_jurys, [
                    'jury' => $president,
                    'role' => $this->defence::JURY_ROLES['PRESIDENT']
                ]);
            } else if ($president_is_new_to_defence) {
                $this->jury::where([
                    'jury_id' => $current_jurys['president']['id'],
                    'defence_id' => $defence->id
                ])->delete();
            }

            foreach ($examiners_removed as $ex) {
                $this->jury::where([
                    'jury_id' => $ex['id'],
                    'defence_id' => $defence->id
                ])->delete();
            }
            foreach ($examiners_not_registered as $email) {
                $new = $this->teacherRepository->create_jury($email);
                array_push($new_jurys, [
                    'jury' => $new,
                    'role' => $this->defence::JURY_ROLES['EXAMINER']
                ]);
            }
            $temp = $this->role::firstWhere('name', $this->role::DEFAULT_ROLES["JURY"]);
            foreach ($new_jurys as $jury) {
                $jury['jury']->roles()->syncWithoutDetaching([$temp->id]);
                $this->jury::create([
                    "jury_id" => $jury['jury']->id,
                    "defence_id" => $defence->id,
                    "role" => $jury['role'],
                ]);
            }

            $defence->update([
                'date' => $request->date,
                'time' => $request->time,
                'room_id' => $request->room_id ?? null,
                'other_place' => $request->other_place ?? null,
                'mode' => $request->mode,
                'nature' => $request->nature,
                'guest' => $request->guest
            ]);
            
            $defence->refresh();
        });
        foreach ($new_jurys as $jury) {
            $jury['jury']->notify(new DefenceInvitationNotification($jury['role'], $defence, Teacher::class));
            if ($jury['jury']->register_verification_hash)
                $jury['jury']->notify(new ConfirmRegisterNotification($jury['jury']->id, $jury['jury']->register_verification_hash));
        }

        // Return success response with defense data
        return [
            'success' => true,
            'message' => 'Defense updated successfully.',
            'defence' => $defence,
        ];
    }

    public function delete($id): array
    {
        try {
            $defence = $this->defence::find($id);
            if (!$defence) {
                return [
                    'success' => false,
                    'message' => 'defence not found',
                ];
            }
            $user_auth = auth()->user();
            if (!$defence->same_establishment($user_auth)) {
                return [
                    'success' => false,
                    'message' => 'Not allowed to delete this defence',
                ];
            }
            $defence->project->update([
                'defence_id' => null
            ]);

            $defence->delete();
            return [
                'success' => true,
                'message' => 'Defence deleted successfully.',
                'defence' => $defence
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "something went wrong. please try again",
            ];
        }
    }

    public function uploadProjectThesisDefFiles(UploadThesisDefFilesRequest $request, $id): array
    {
        $files = $request['files'] ?? [];
        $files_types = $request->files_types ?? [];
        $old_files = $request->old_files ?? [];
        if (count($files) !== count($files_types)) {
            return [
                "success" => false,
                "message" => "Please provide a file for each file type",
            ];
        }
        $defence = $this->defence->find($id);
        if (!$defence) {
            return [
                'success' => false,
                'message' => 'defence not found'
            ];
        }
        if (!$defence->project->has_member(auth()->id())) {
            return [
                'success' => false,
                'message' => 'you are not a member in this project'
            ];
        }

        $files = $this->handleFiles($files, $files_types, $defence, $old_files);
        $defence->update([
            'files' => (object) $files
        ]);
        return [
            "success" => true,
            "message" => "thesis defence files uploaded successfully",
            "defence" => $defence
        ];
    }


    private function handleFiles($files, $files_types, $defence, $old_files)
    {
        $defence_urls = array_column($defence->files ?? [], "link");
        $kept_files = array_intersect($defence_urls, $old_files ?? []);
        $result = [];
        foreach ($defence->files ?? [] as $type => $value) {
            $path = 'public' . str_replace(asset('storage'), '', $value['link']);
            if (in_array($value['link'], $kept_files)) {
                $result[$type] = $value;
            } else {
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
            }
        }
        for ($i = 0; $i < count($files); $i++) {
            $path = $files[$i]->storeAs('defences', Carbon::now()->timestamp . '.' . $files[$i]->getClientOriginalName(), 'public');
            $url = asset('storage/' . $path);
            $result[$files_types[$i]] = [
                'link' => $url,
                'name' => $files[$i]->getClientOriginalName()
            ];
        }
        return $result;
    }
}
