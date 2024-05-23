<?php

namespace App\Http\Repositories;

use Exception;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Period;
use App\Models\Project;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Headmaster;

use App\Models\ProjectMember;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Interfaces\ProjectInterface;
use App\Models\Internship_service_member;
use App\Models\Scientific_committee_member;
use App\Http\Requests\Project\ChangeStatusRequest;
use App\Notifications\ConfirmRegisterNotification;
use App\Http\Requests\Project\SubmitProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Requests\Project\UpdateProgressRequest;
use App\Notifications\ProjectInvitationNotification;
use App\Notifications\ProjectSubmissionNotification;
use App\Http\Requests\Project\AuthorizeDefenceRequest;
use App\Notifications\ProjectValidationNotification;

class ProjectRepository implements ProjectInterface
{
    public function __construct(
        private Project $project,
        private Period $period,
        private Role $role,
        private ProjectMember $project_member,
        private User $user,
        private StudentRepository $studentRepository,
        private TeacherRepository $teacherRepository
    ) {
    }


    private function can_submit_project(User $user): bool
    {
        return $user->person_type === Teacher::class ||
            ($user->person_type === Student::class && !$this->project_member::where('member_id', $user->id)->exists());
    }
    private function validate_member(User $member): array
    {
        if ($member->person_type !== Student::class) {
            return [
                "success" => false,
                'message' => "Member with email " . $member->email . " is not a student"
            ];
        }

        $exists = $this->project_member::where('member_id', $member->id)->exists();
        if ($exists) {
            return [
                "success" => false,
                "message" => "Member with email " . $member->email . " is already in another project"
            ];
        }
        return ['success' => true];
    }


    public function all(): array
    {
        try {
            $projects = $this->project::all();
            return [
                "success" => true,
                "message" => "All projects have been gotten successfully",
                "projects" => $projects
            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => "Something went wrong , please try again"
            ];
        }
    }

    public function getAuthProjects(): array
    {
        /** @var User */
        $user = auth()->user();
        switch ($user->person_type) {
            case Student::class:
                return [
                    "success" => true,
                    "message" => "Project retreived successfully",
                    "project" => $user->member_project
                ];
            case Teacher::class:
                return [
                    "success" => true,
                    "message" => "Projects retreived successfully",
                    "projects" => $user->all_projects()
                ];
            case Scientific_committee_member::class:
            case Internship_service_member::class:
            case Headmaster::class:
                return [
                    "success" => true,
                    "message" => "Establishment projects retreived successfully",
                    "projects" => $user->person->establishment->projects
                ];
            default:
                return [
                    "success" => false,
                    "message" => "You are not allowed to view projects"
                ];
        }
    }

    public function destroy($id): array
    {
        try {
            $project = $this->project::find($id);
            if (!$project) {
                return [
                    "success" => false,
                    "message" => "No project found"
                ];
            }
            if (!$this->period::is_period($this->period::PROJECT_PERIODS['SUBMISSION'],  $project->establishment_id)) {
                return [
                    "success" => true,
                    'message' => 'Can not withdraw project at this point'
                ];
            }
            if (!$project->belongs_to(auth()->id())) {
                return [
                    "success" => false,
                    "message" => "Not allowed to withdraw submission of this project"
                ];
            }
            DB::transaction(function () use ($project) {
                ProjectMember::where('project_id', $project->id)->delete();
                $project->delete();
            });
            return [
                "success" => true,
                "message" => "Project submission withdrawn successfully",
                "project" => $project
            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => "Something went wrong , please try again"
            ];
        }
    }

    public function show($id): array
    {
        try {
            $project = $this->project::find($id);
            $user = auth()->user();
            if (!$project) {
                return [
                    "success" => false,
                    "message" => "Project not found",
                ];
            }

            if (!$project->allow_view($user)) {
                return [
                    "success" => false,
                    "message" => "Not allowed to view this project",
                ];
            }
            return [
                "success" => true,
                "message" => "Project has been retrieved successfully",
                "project" => $project
            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => "Something went wrong , please try again"
            ];
        }
    }

    public function changeStatus(ChangeStatusRequest $request): array
    {
        try {
            $project = $this->project::find($request->project);
            if (!$project) {
                return [
                    "success" => false,
                    "message" => "Project not found",
                ];
            }

            $user = auth()->user();
            if (!$project->same_establishment($user)) {
                return [
                    "success" => false,
                    "message" => "Can't update other projects"
                ];
            }
            if($project->is_accepted() || $project->is_refused()){
                return [
                    'success' => false,
                    'message' => 'project already validated'
                ];
            }

            if ($project->status === $this->project::STATUSES["PENDING"]) {
                if (!$this->period::is_period($this->period::PROJECT_PERIODS["VALIDATION"], $project->establishment_id))
                    return [
                        'success' => false,
                        'message' => 'We are not in the ' . $this->period::PROJECT_PERIODS["VALIDATION"],
                    ];
                $project->update([
                    'status' => $request->status,
                    'decision_date' => Carbon::today()->format('Y-m-d')
                ]);
            }

            if ($project->status === $this->project::STATUSES["RECOURSE"]) {
                if ($request->status === $this->project::STATUSES["RECOURSE"])
                    return [
                        'success' => false,
                        'message' => 'Please choose accepted or refused'
                    ];
                if (!$this->period::is_period($this->period::PROJECT_PERIODS["RECOURSE_VALIDATION"], $project->establishment_id))
                    return [
                        'success' => false,
                        'message' => 'We are not in the ' . $this->period::PROJECT_PERIODS["RECOURSE_VALIDATION"],
                    ];
                $project->update([
                    'status' => $request->status,
                    'recourse_decision_date' => Carbon::today()->format('Y-m-d')
                ]);
            }

            $project->supervisor->notify(new ProjectValidationNotification($project, Teacher::class));
            if ($project->co_supervisor)
                $project->co_supervisor->notify(new ProjectValidationNotification($project, Teacher::class));
            foreach ($project->members as $member) {
                $member->notify(new ProjectValidationNotification($project, Student::class));
            }

            return [
                "success" => true,
                'message' => 'Project status changed successfully',
                "project" => $project
            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => "Something went wrong , please try again"
            ];
        }
    }

    public function submitProject(SubmitProjectRequest $request): array
    {
        /** @var User */
        $project_holder = auth()->user();
        if (!$this->can_submit_project($project_holder)) {
            return [
                "success" => false,
                "message" => "you can not submit a project",
            ];
        }

        if (!$this->period::is_period($this->period::PROJECT_PERIODS['SUBMISSION'], $project_holder->person->establishment_id)) {
            return [
                'success' => false,
                'message' => 'we are not in the project submission period',
            ];
        }

        $members = $request->members ?? [];
        $members_not_registered = [];
        $supervisor_is_new = false;
        $co_supervisor_is_new = false;
        $members_registered = [];

        if ($request->co_supervisor) {
            $co_supervisor = $this->user::firstWhere('email', $request->co_supervisor);
            if (!$co_supervisor) $co_supervisor_is_new = true;
            else {
                if ($co_supervisor->person_type !== Teacher::class)
                    return [
                        "success" => false,
                        "message" => "the co-supervisor you provided isn't a teacher"
                    ];
            }
        }

        if ($project_holder->person_type === Student::class) {
            if (!$request->supervisor)
                return [
                    "success" => false,
                    "message" => "Please provide a supervisor"
                ];

            array_push($members_registered, $project_holder);
            $members = array_filter($members, function ($member) use ($project_holder) {
                return $member !== $project_holder->email;
            });
            if (count($members) === 6) return [
                'success' => false,
                'message' => 'You provided one extra member',
            ];

            $supervisor = $this->user::firstWhere('email', $request->supervisor);
            if (!$supervisor) $supervisor_is_new = true;
            else {
                if ($supervisor->person_type !== Teacher::class)
                    return [
                        "success" => false,
                        "message" => "the supervisor you provided isn't a teacher"
                    ];
            }
        } else {
            if (count($members) === 0)
                return [
                    "success" => false,
                    "message" => "Please provide at least one team member",
                ];
            $supervisor = clone $project_holder;
        }

        foreach ($members as $member_email) {
            $member = $this->user::firstWhere('email', $member_email);
            if ($member) {
                $res = $this->validate_member($member);
                if (!$res['success']) return $res;
                array_push($members_registered, $member);
            } else array_push($members_not_registered, $member_email);
        }

        /** @var Project */
        $project = null;
        DB::transaction(function () use (
            $project_holder,
            &$supervisor,
            &$co_supervisor,
            &$members_not_registered,
            $request,
            &$members_registered,
            &$project,
            $supervisor_is_new,
            $co_supervisor_is_new
        ) {
            $project_holder->roles()
                ->syncWithoutDetaching([
                    $this->role::firstWhere('name', $this->role::DEFAULT_ROLES["PROJECT_HOLDER"])->id
                ]);
            $temp = null;
            if ($supervisor_is_new)
                $supervisor = $this->teacherRepository->create_supervisor($request->supervisor);
            else {
                $temp = $this->role::firstWhere('name', $this->role::DEFAULT_ROLES["SUPERVISOR"]);
                $supervisor->roles()->syncWithoutDetaching([$temp->id]);
            }
            if ($co_supervisor_is_new)
                $co_supervisor = $this->teacherRepository->create_supervisor($request->co_supervisor);
            else if ($co_supervisor) {
                if (!$temp) $temp = $this->role::firstWhere('name', $this->role::DEFAULT_ROLES["SUPERVISOR"]);
                $co_supervisor->roles()->syncWithoutDetaching([$temp->id]);
            }

            $project = $this->project->create([
                'type' => $request->type,
                'trademark_name' => $request->trademark_name,
                'scientific_name' => $request->scientific_name,
                'resume' => $request->resume,
                'establishment_id' => $project_holder->person->establishment_id,
                'project_holder_id' => $project_holder->id,
                'supervisor_id' =>  $supervisor->id,
                'progress' => (object) [],
                'files' => (object) [],
                'co_supervisor_id' => $request->co_supervisor ? $co_supervisor->id : null,
                'updated_at' => null
            ]);

            foreach ($members_not_registered as $member_email) {
                $new = $this->studentRepository->create_student($member_email);
                array_push($members_registered, $new);
            }
            foreach ($members_registered as $member) {
                $member->roles()->syncWithoutDetaching(
                    [$this->role::firstWhere('name', $this->role::DEFAULT_ROLES["PROJECT_MEMBER"])->id]
                );
                $this->project_member::create([
                    "member_id" => $member->id,
                    "project_id" => $project->id
                ]);
            }
            $project->refresh();
        });

        $project_holder->notify(new ProjectSubmissionNotification($project, $project_holder->person_type));
        $supervisor->notify(new ProjectInvitationNotification("supervisor", $project, Teacher::class));
        if ($co_supervisor)
            $co_supervisor->notify(new ProjectInvitationNotification("co-supervisor", $project, Teacher::class));

        if ($supervisor_is_new)
            $supervisor->notify(new ConfirmRegisterNotification($supervisor->id, $supervisor->register_verification_hash));
        if ($co_supervisor_is_new)
            $co_supervisor->notify(new ConfirmRegisterNotification($co_supervisor->id, $co_supervisor->register_verification_hash));

        foreach ($members_registered as $member) {
            $member->notify(new ProjectInvitationNotification("project member", $project, Student::class));
            if ($member->register_verification_hash)
                $member->notify(new ConfirmRegisterNotification($member->id, $member->register_verification_hash));
        }

        return [
            'success' => true,
            'message' =>  "Project submitted successfully. An email was sent to all participants.",
            'project' => $project
        ];
        /* } catch (\Exception $e) {
            return [
                'success' => false,
                'message' =>  "Something went wrong when submitting project"
            ];
        } */
    }

    public function updateProject(UpdateProjectRequest $request, $id): array
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
        $project = $this->project::find($id);
        if (!$project) {
            return [
                "success" => false,
                "message" => "No Project found",
            ];
        }
        if (
            (!$this->period::is_period($this->period::PROJECT_PERIODS['SUBMISSION'], $project->establishment_id)) &&
            ($project->status !== $this->project::STATUSES['RECOURSE'] ||
                !$this->period::is_period($this->period::PROJECT_PERIODS['RECOURSE'], $project->establishment_id))
        ) {
            return [
                "success" => false,
                'message' => 'Can not update project at this point'
            ];
        }
        $project_holder = auth()->user();
        if (!$project->belongs_to($project_holder->id)) {
            return [
                "success" => false,
                "message" => "you're not allowed to update this project"
            ];
        }

        $members = $request->members ?? [];
        $members_not_registered = [];
        $members_new_model = [];
        $members_new = array_diff($members, $project->members->pluck('email')->toArray());
        $members_removed = array_diff($project->members->pluck('email')->toArray(), $members);
        $supervisor_is_new = false;
        $supervisor_is_new_to_project = false;
        $co_supervisor_is_new_to_project = false;
        $co_supervisor_is_new = false;

        if ($request->co_supervisor) {
            $co_supervisor = $this->user::firstWhere('email', $request->co_supervisor);
            if (!$co_supervisor) {
                $co_supervisor_is_new = true;
                $co_supervisor_is_new_to_project = true;
            } else {
                if ($project->co_supervisor_id !== $co_supervisor->id) {
                    if ($co_supervisor->person_type !== Teacher::class)
                        return [
                            "success" => false,
                            "message" => "the co-supervisor you provided isn't a teacher"
                        ];
                    $co_supervisor_is_new_to_project = true;
                }
            }
        }

        if ($project_holder->person_type === Student::class) {
            if (!$request->supervisor)
                return [
                    "success" => false,
                    "message" => "please provide a supervisor"
                ];

            $members = array_filter($members, function ($member) use ($project_holder) {
                return $member !== $project_holder->email;
            });
            $members_removed = array_filter($members_removed, function ($member) use ($project_holder) {
                return $member !== $project_holder->email;
            });
            if (count($members) === 6) return [
                'success' => false,
                'message' => 'You provided one extra member',
            ];

            $supervisor = $this->user->firstWhere('email', $request->supervisor);
            if (!$supervisor) {
                $supervisor_is_new = true;
                $supervisor_is_new_to_project = true;
            } else {
                if ($project->supervisor_id !== $supervisor->id) {
                    if ($supervisor->person_type !== Teacher::class)
                        return [
                            "success" => false,
                            "message" => "the supervisor you provided isn't a teacher"
                        ];
                    $supervisor_is_new_to_project = true;
                }
            }
        } else {
            if (count($members) === 0) {
                return [
                    "success" => false,
                    "message" => "Please provide at least one team member",
                ];
            }
            $supervisor = clone $project_holder;
        }

        foreach ($members_new as $member_email) {
            $member = $this->user::firstWhere('email', $member_email);
            if ($member) {
                $res = $this->validate_member($member);
                if (!$res['success']) return $res;
                array_push($members_new_model, $member);
            } else
                array_push($members_not_registered, $member_email);
        }

        DB::transaction(function () use (
            $supervisor_is_new_to_project,
            $co_supervisor_is_new_to_project,
            $supervisor_is_new,
            $co_supervisor_is_new,
            &$supervisor,
            &$co_supervisor,
            &$members_new_model,
            &$members_not_registered,
            $request,
            &$members_removed,
            &$project,
            $files,
            $files_types,
            $old_files
        ) {
            $temp = null;
            if ($supervisor_is_new)
                $supervisor = $this->teacherRepository->create_supervisor($request->supervisor);
            else if ($supervisor_is_new_to_project) {
                $temp = $this->role::firstWhere('name', $this->role::DEFAULT_ROLES["SUPERVISOR"]);
                $supervisor->roles()->syncWithoutDetaching([$temp->id]);
            }

            if ($co_supervisor_is_new)
                $co_supervisor = $this->teacherRepository->create_supervisor($request->co_supervisor);
            else if ($co_supervisor_is_new_to_project) {
                if (!$temp)
                    $temp = $this->role::firstWhere('name', $this->role::DEFAULT_ROLES["SUPERVISOR"]);
                $co_supervisor->roles()->syncWithoutDetaching([$temp->id]);
            }

            $member_role = $this->role::firstWhere('name', $this->role::DEFAULT_ROLES["PROJECT_MEMBER"]);
            foreach ($members_removed as $member_email) {
                $member = $this->user::firstWhere('email', $member_email);
                $member->roles()->detach($member_role->id);
                $this->project_member::where('member_id', $member->id)->delete();
            }
            foreach ($members_not_registered as $member_email) {
                $new = $this->studentRepository->create_student($member_email);
                array_push($members_new_model, $new);
            }

            $files = $this->handleFiles($files, $files_types, $project, $old_files);
            $project->update([
                'type' => $request->type,
                'trademark_name' => $request->trademark_name,
                'scientific_name' => $request->scientific_name,
                'resume' => $request->resume,
                'supervisor_id' =>  $supervisor->id,
                'co_supervisor_id' => $request->co_supervisor ? $co_supervisor->id : null,
                'files' => $files
            ]);

            foreach ($members_new_model as $member) {
                $member->roles()->syncWithoutDetaching([$member_role->id]);
                $this->project_member::create([
                    "member_id" => $member->id,
                    "project_id" => $project->id
                ]);
            }
            $project->refresh();
        });

        if ($supervisor_is_new_to_project)
            $supervisor->notify(new ProjectInvitationNotification("supervisor", $project, Teacher::class));
        if ($co_supervisor_is_new_to_project)
            $co_supervisor->notify(new ProjectInvitationNotification("co-supervisor", $project, Teacher::class));

        if ($supervisor_is_new)
            $supervisor->notify(new ConfirmRegisterNotification($supervisor->id, $supervisor->register_verification_hash));
        if ($co_supervisor_is_new)
            $co_supervisor->notify(new ConfirmRegisterNotification($co_supervisor->id, $co_supervisor->register_verification_hash));

        foreach ($members_new_model as $member) {
            $member->notify(new ProjectInvitationNotification("project member", $project, Student::class));
            if (in_array($member->email, $members_not_registered))
                $member->notify(new ConfirmRegisterNotification($member->id, $member->register_verification_hash));
        }

        return [
            'success' => true,
            'message' =>  "Project updated successfully. An email was sent to new participants.",
            'project' => $project
        ];
    }

    public function updateProgress(UpdateProgressRequest $request, $id): array
    {
        $project = $this->project->find($id);
        if (!$project) {
            return [
                'success' => false,
                'message' => 'project not found'
            ];
        }
        $user = auth()->user();
        if (!$project->is_supervised_by($user)) {
            return [
                'success' => false,
                'message' => 'progress can only be updated by the supervisor'
            ];
        }
        if (!$project->is_accepted()) {
            return [
                'success' => false,
                'message' => 'project was not accepted by committee'
            ];
        }
        $pr = $project->progress ?? (object) [];
        $max = count(array_keys($pr)) > 0 ? max(array_keys($pr)) : 0;
        if ($request->progress <= $max) {
            return [
                'success' => false,
                'message' =>  "can not put a progress less than the actual",
            ];
        }

        $temp = $project->progress;
        $temp[$request->progress] = $request->observation ?? "";
        $project->progress = $temp;
        $project->save();
        return [
            "success" => true,
            "message" => "progress updated successfully",
            "project" => $project
        ];
    }

    public function authorizeDefence(AuthorizeDefenceRequest $request, $id): array
    {
        $project = $this->project->find($id);
        if (!$project) {
            return [
                'success' => false,
                'message' => 'project not found'
            ];
        }
        if (!$project->is_accepted()) {
            return [
                'success' => false,
                'message' => 'project was not accepted by committee'
            ];
        }
        if ($project->is_authorized_defence) {
            return [
                'success' => false,
                'message' => 'project is already authorized'
            ];
        }
        /** @var User */
        $user = auth()->user();
        $project = $this->project->find($id);
        if (!$project->is_supervised_by($user)) {
            return [
                'success' => false,
                'message' => 'progress can only be updated by the supervisor'
            ];
        }

        $temp = $project->files;
        $project->is_authorized_defence  = true;
        if ($request['file']) {
            $path = $request->file->storeAs('projects', Carbon::now()->timestamp . '.' . $request['file']->getClientOriginalName(), 'public');
            $url = asset('storage/' . $path);
            $temp[$this->project::FILES_TYPES['AUTHORIZATION_FILE']] = [
                'link' => $url,
                'name' => $request['file']->getClientOriginalName()
            ];
            $project->files = $temp;
        }
        $project->save();
        return [
            "success" => true,
            "message" => "authorization accorded successfully",
            "project" => $project
        ];
    }

    public function get_project_progress($id): array
    {
        $project = $this->project->find($id);
        if (!$project) {
            return [
                'success' => false,
                'message' => 'project not found'
            ];
        }
        if (!$project->is_accepted()) {
            return [
                'success' => false,
                'message' => 'project was not accepted by committee'
            ];
        }
        /** @var User */
        $user = auth()->user();
        if (!$project->allow_view($user)) {
            return [
                'success' => false,
                'message' => 'you can not view observations of this project'
            ];
        }
        return [
            "success" => true,
            "message" => "observations retrieved successfully",
            "progress" => $project->progress
        ];
    }

    private function handleFiles($files, $files_types, $project, $old_files)
    {
        if (!$files) $files = [];
        $project_urls = array_column($project->files ?? [], "link");
        $kept_files = array_intersect($project_urls, $old_files ?? []);
        $result = [];
        foreach ($project->files ?? [] as $type => $value) {
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
            $path = $files[$i]->storeAs('projects', Carbon::now()->timestamp . '.' . $files[$i]->getClientOriginalName(), 'public');
            $url = asset('storage/' . $path);
            $result[$files_types[$i]] = [
                'link' => $url,
                'name' => $files[$i]->getClientOriginalName()
            ];
        }
        return $result;
    }
}
