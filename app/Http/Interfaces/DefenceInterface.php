<?php

namespace App\Http\Interfaces;

use App\Http\Requests\Defence\CreateDefenseRequset;
use App\Http\Requests\Defence\UpdateDefenceRequest;
use App\Http\Requests\Defence\UploadThesisDefFilesRequest;

interface DefenceInterface
{
    public function all(): array;
    public function getById($id): array;
    public function create_defence(CreateDefenseRequset $request, $project_id): array;
    public function uploadProjectThesisDefFiles(UploadThesisDefFilesRequest $request, $id): array;
    public function update_defence(UpdateDefenceRequest $request, $id): array;
    public function delete($id): array;
}
