<?php

namespace App\Http\Interfaces;

use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\SubmitTaskRequest;
use App\Http\Requests\Task\TaskValidationRequest;
use App\Http\Requests\Task\UpdateTaskRequest;

interface TaskInterface
{   
    public function all(): array;
    public function readByProject($project_id): array;
    public function getById($id): array;
    public function create(CreateTaskRequest $request, $project_id) : array;
    public function update(UpdateTaskRequest $request, $task_id) :array;
    public function destroy($task_is): array;
    public function validateTask(TaskValidationRequest $request, $task_id) : array;
    public function submit(SubmitTaskRequest $request, $task_id): array;
}
