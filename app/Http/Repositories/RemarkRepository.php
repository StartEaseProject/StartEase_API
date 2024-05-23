<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\RemarkInterface;
use App\Http\Requests\Remark\CreateRemarkRequest;
use App\Http\Requests\Remark\UpdateRemarkRequest;
use App\Models\Period;
use App\Models\Project;
use App\Models\Remark;
use Exception;

class RemarkRepository implements RemarkInterface
{
    public function __construct(
        private Remark $remark,
        private Project $project,
        private Period $period
    ){}

    public function destroy($id): array
    {
        $remark = $this->remark::find($id);
        if (!$remark) {
            return [
                "success" => false,
                "message" => "remark not found"
            ];
        }
        if (!$remark->belongs_to(auth()->id())) {
            return [
                "success" => false,
                "message" => "Not allowed to delete this project remark",
            ];
        }
        $remark->delete();
        return [
            "success" => true,
            "message" => "remark deleted"
        ];
    }

    public function get_by_projectId($id): array
    {
        $project = $this->project::find($id);
        if (!$project) {
            return [
                "success" => false,
                "message" => "Project not found",
            ];
        }
        if (!$project->allow_view(auth()->user())) {
            return [
                "success" => false,
                "message" => "Not allowed to view this project remarks",
            ];
        }
        return [
            'success' => true,
            'message' => 'remarks retreived',
            'remarks' => $project->remarks
        ];
    }

    public function update(UpdateRemarkRequest $request): array
    {
        try {
            $remark = $this->remark::find($request['id']);
            if (!$remark) {
                return [
                    "success" => false,
                    "message" => "remark not found"
                ];
            }
            if (!$remark->belongs_to(auth()->id())) {
                return [
                    "success" => false,
                    "message" => "Not allowed to update this project remark",
                ];
            }

            $remark->update([
                'content' => $request['content']
            ]);
            return [
                'success' => true,
                'message' => 'remark updated',
                'remark' => $remark
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "Something went wrong"
            ];
        }
    }

    public function create(CreateRemarkRequest $request): array
    {
        $project = $this->project::find($request->project_id);
        if (!$project) {
            return [
                "success" => false,
                "message" => "project not found",
            ];
        }
        $user = auth()->user();
        if (!$project->allow_view($user)) {
            return [
                "success" => false,
                "message" => "Not allowed to create a remark in this project",
            ];
        }
        try {
            $remark = $this->remark::create([
                'project_id' => $request->project_id,
                'user_id' => $user->id,
                'content' => $request->content,
                'updated_at' => null
            ]);
            return [
                'success' => true,
                'message' => 'project remark added',
                'remark' => $remark
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "Something went wrong"
            ];
        }
    }
}
