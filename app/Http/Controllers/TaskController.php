<?php

namespace App\Http\Controllers;

use App\Http\Interfaces\TaskInterface;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\SubmitTaskRequest;
use App\Http\Requests\Task\TaskValidationRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use Illuminate\Http\Response;

class TaskController extends BaseController
{
    public function __construct(
        private TaskInterface $taskRepository
    ){}

    
    public function index()
    {
        $response = $this->taskRepository->all();
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'tasks' => TaskResource::collection($response['tasks'])
            ]);
    }

    public function store(CreateTaskRequest $request, $project_id)
    {
        $response = $this->taskRepository->create($request, $project_id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'task' => new TaskResource($response['task'])
            ]);
        
    }

    public function readByProject($project_id)
    {
        $response = $this->taskRepository->readByProject($project_id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'tasks' => TaskResource::collection($response['tasks'])
            ]);
    }

    public function show($id)
    {
        $response = $this->taskRepository->getById($id);
        return !$response['success'] ?
        $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'task' => new TaskResource($response['task'])
            ]);
    }

    public function update(UpdateTaskRequest $request, $task_id)
    {
        $response = $this->taskRepository->update($request, $task_id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'task' => new TaskResource($response['task'])
            ]);
    }

    public function destroy($task_id)
    {
        $response = $this -> taskRepository -> destroy($task_id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'task' => new TaskResource($response['task'])
            ]);
    }

    public function validateTask(TaskValidationRequest $request, $task_id)
    {
        $response = $this->taskRepository -> validateTask($request, $task_id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'task' => new TaskResource($response['task'])
            ]);
    }

    public function submit(SubmitTaskRequest $request, $task_id)
    {
        $response = $this->taskRepository->submit($request, $task_id);
        return !$response['success'] ?
            $this->sendError($response['message'], Response::HTTP_INTERNAL_SERVER_ERROR) :
            $this->sendResponse($response['message'], [
                'task' => new TaskResource($response['task'])
            ]);
    }
}
