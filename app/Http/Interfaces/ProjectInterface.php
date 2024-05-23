<?php

namespace App\Http\Interfaces;

use App\Http\Requests\Project\AuthorizeDefenceRequest;
use App\Http\Requests\Project\ChangeStatusRequest;
use App\Http\Requests\Project\SubmitProjectRequest;
use App\Http\Requests\Project\UpdateProgressRequest;
use App\Http\Requests\Project\UpdateProjectRequest;

interface ProjectInterface
{
    public function submitProject(SubmitProjectRequest $request): array;
    public function updateProject(UpdateProjectRequest $request, $id): array;
    public function all(): array;
    public function getAuthProjects(): array;
    public function show($id): array;
    public function get_project_progress($id): array;
    public function changeStatus(ChangeStatusRequest $request): array;
    public function destroy($id): array;
    public function updateProgress(UpdateProgressRequest $request, $id): array;
    public function authorizeDefence(AuthorizeDefenceRequest $request, $id): array;
}
