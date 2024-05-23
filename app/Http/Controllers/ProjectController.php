<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Response;
use App\Http\Resources\ProjectResource;
use App\Http\Interfaces\ProjectInterface;
use App\Http\Requests\Project\ChangeStatusRequest;
use App\Http\Requests\Project\SubmitProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Requests\Project\UpdateProgressRequest;
use App\Http\Requests\Project\AuthorizeDefenceRequest;

class ProjectController extends BaseController
{
    public function __construct(
        private ProjectInterface $projectRepository
    ){}

    
    public function index()
    {
        $response = $this->projectRepository->all();
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'projects' => ProjectResource::collection($response['projects'])
            ]);
    }

    public function auth()
    {
        $response = $this->projectRepository->getAuthProjects();
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_FORBIDDEN) :
            $this->sendResponse($response['message'], [
                'project' => isset($response['project'])  ? new ProjectResource($response['project']) : null,
                'projects' => isset($response['projects']) ? ProjectResource::collection($response['projects']) : null,
            ]);
    }

    public function show($id)
    {
        $response = $this->projectRepository->show($id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_FORBIDDEN) :
            $this->sendResponse($response['message'], [
                'project' => new ProjectResource($response['project'])
            ]);
    }

    public function get_progress($id)
    {
        $response = $this->projectRepository->get_project_progress($id);
        return !$response['success'] ?
        $this->sendError($response['message'], Response::HTTP_FORBIDDEN) :
            $this->sendResponse($response['message'], [
                'progress' => $response['progress']
            ]);
    }

    public function store(SubmitProjectRequest $request)
    {
        $response = $this->projectRepository->submitProject($request);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_BAD_REQUEST) :
            $this->sendResponse($response['message'], [
                'project' => new ProjectResource($response['project'])
            ]);
    }

    public function update(UpdateProjectRequest $request, $id)
    {
        $response = $this->projectRepository->updateProject($request, $id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_BAD_REQUEST) :
            $this->sendResponse($response['message'], [
                'project' => new ProjectResource($response['project'])
            ]);
    }

    public function changeStatus(ChangeStatusRequest $request)
    {
        $response = $this->projectRepository->changeStatus($request);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'project' => new ProjectResource($response['project'])
            ]);
    }
    
    public function updateProgress(UpdateProgressRequest $request,$id)
    {
        $response = $this->projectRepository->updateProgress($request,$id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'project' => new ProjectResource($response['project'])
            ]);
    }
    public function authorize_defence(AuthorizeDefenceRequest $request, $id)
    {
        $response = $this->projectRepository->authorizeDefence($request, $id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'project' => new ProjectResource($response['project'])
            ]);
    }
    public function destroy($id)
    {
        $response = $this->projectRepository->destroy($id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'project' => new ProjectResource($response['project'])
            ]);
    }

    public function getParams()
    {
        return $this->sendResponse("Project params retreived successfully", [
            "types" => Project::TYPES,
            "statuses" => Project::UI_STATUSES,
            "project_files_types" => Project::FILES_TYPES
        ]);
    }
}
