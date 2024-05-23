<?php

namespace App\Http\Controllers;

use App\Http\Resources\AnnouncementResource;
use App\Http\Resources\PeriodResource;
use App\Models\Announcement;
use App\Models\Defence;
use App\Models\Establishment;
use App\Models\Filiere;
use App\Models\Grade;
use App\Models\Headmaster;
use App\Models\Internship_service_member;
use App\Models\Project;
use App\Models\Role;
use App\Models\Scientific_committee_member;
use App\Models\Speciality;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Response;


class DashboardController extends BaseController
{
    public function admin()
    {
        return $this->sendResponse("Dashboard data retreived", [
            'establishment_count' => Establishment::count(),
            'filiere_count' => Filiere::count(),
            'speciality_count' => Speciality::count(),
            'grade_count' => Grade::count(),
            'project_count' => Project::count(),
            'defence_count' => Defence::count(),
            'user_count' => [
                User::MODELSTOTYPES[Student::class] => Student::count(),
                User::MODELSTOTYPES[Teacher::class] => Teacher::count(),
                User::MODELSTOTYPES[Scientific_committee_member::class] => Scientific_committee_member::count(),
                User::MODELSTOTYPES[Internship_service_member::class] => Internship_service_member::count(),
                User::MODELSTOTYPES[Headmaster::class] => Headmaster::count(),
            ]
        ]);
    }

    public function student()
    {
        /** @var User */
        $auth = auth()->user();
        if ($auth->person_type!==Student::class)
            return $this->sendError("You are not allowed", Response::HTTP_FORBIDDEN);

        return $this->sendResponse("Dashboard data retreived", [
            'periods' => PeriodResource::collection(auth()->user()->person->establishment->periods)
        ]);
    }

    public function teacher()
    {
        /** @var User */
        $auth = auth()->user();
        if ($auth->person_type !== Teacher::class)
            return $this->sendError("You are not allowed", Response::HTTP_FORBIDDEN);

        $res1 = $auth->submitted_projects();
        $res2 = $auth->supervised_projects();
        $res3 = $auth->co_supervised_projects();

        return $this->sendResponse("Dashboard data retreived", [
            'by_status' => [
                Project::STATUSES['PENDING'] => $res1->where('status', Project::STATUSES['PENDING'])->count() + $res2->where('status', Project::STATUSES['PENDING'])->count() + $res3->where('status', Project::STATUSES['PENDING'])->count(),
                Project::STATUSES['ACCEPTED'] => $res1->where('status', Project::STATUSES['ACCEPTED'])->count() + $res2->where('status', Project::STATUSES['ACCEPTED'])->count() + $res3->where('status', Project::STATUSES['ACCEPTED'])->count(),
                Project::STATUSES['REFUSED'] => $res1->where('status', Project::STATUSES['REFUSED'])->count() + $res2->where('status', Project::STATUSES['REFUSED'])->count() + $res3->where('status', Project::STATUSES['REFUSED'])->count(),
                Project::STATUSES['RECOURSE'] => $res1->where('status', Project::STATUSES['RECOURSE'])->count() + $res2->where('status', Project::STATUSES['RECOURSE'])->count() + $res3->where('status', Project::STATUSES['RECOURSE'])->count(),
                Project::STATUSES['RECOURSE_ACCEPTED'] => $res1->where('status', Project::STATUSES['RECOURSE_ACCEPTED'])->count() + $res2->where('status', Project::STATUSES['RECOURSE_ACCEPTED'])->count() + $res3->where('status', Project::STATUSES['RECOURSE_ACCEPTED'])->count(),
                Project::STATUSES['RECOURSE_REFUSED'] => $res1->where('status', Project::STATUSES['RECOURSE_REFUSED'])->count() + $res2->where('status', Project::STATUSES['RECOURSE_REFUSED'])->count() + $res3->where('status', Project::STATUSES['RECOURSE_REFUSED'])->count(),
            ],
            'by_role' => [
                'supervisor' => $auth->supervised_projects->count(),
                'co_supervisor' => $auth->co_supervised_projects->count(),
                'project_holder' => $auth->submitted_projects->count()
            ],
        ]);
    }

    public function other()
    {
        /** @var User */
        $auth = auth()->user();
        if ($auth->person_type!== Scientific_committee_member::class && $auth->person_type !== Internship_service_member::class && $auth->person_type !== Headmaster::class)
            return $this->sendError("You are not allowed", Response::HTTP_FORBIDDEN);

        $projects =$auth->person->establishment->projects();
        return $this->sendResponse("Dashboard data retreived", [
            'by_status' => [
                Project::STATUSES['PENDING'] => $projects->where('status', Project::STATUSES['PENDING'])->count(),
                Project::STATUSES['ACCEPTED'] => $projects->where('status', Project::STATUSES['ACCEPTED'])->count(),
                Project::STATUSES['REFUSED'] => $projects->where('status', Project::STATUSES['REFUSED'])->count(),
                Project::STATUSES['RECOURSE'] => $projects->where('status', Project::STATUSES['RECOURSE'])->count(),
                Project::STATUSES['RECOURSE_ACCEPTED'] => $projects->where('status', Project::STATUSES['RECOURSE_ACCEPTED'])->count(),
                Project::STATUSES['RECOURSE_REFUSED'] => $projects->where('status', Project::STATUSES['RECOURSE_REFUSED'])->count(),
            ],
        ]);
    }

    public function landing()
    {
        return $this->sendResponse("data retreived", [
            'experts' => User::where('person_type', Headmaster::class)->orderBy('created_at', 'desc')->get()->take(4)->pluck('photo_url'),
            'establishments' => Establishment::all()->pluck('logo'),
            'establishment_count' => Establishment::count(),
            'student_count' => Student::count(),
            'project_count' => Project::count(),
            'announcements' => Announcement::where('visibility', Announcement::VISIBILITY['PUBLIC'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get(['title', 'description', 'created_at', 'photo', 'id']),
        ]);
    }

    public function show_announcement($id)
    {
        try {
            return $this->sendResponse("anouncement retreived", [
                'announcement' => new AnnouncementResource(Announcement::where('visibility', Announcement::VISIBILITY['PUBLIC'])->findOrFail($id))
            ]);
        } catch (\Throwable $th) {
            return $this->sendError("Announcement not found", Response::HTTP_NOT_FOUND);
        }
        
    }
}
