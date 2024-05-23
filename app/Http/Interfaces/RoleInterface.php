<?php

namespace App\Http\Interfaces;

use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Requests\Role\CreateRoleRequest;

interface RoleInterface
{   
    public function all():array;
    public function getRole($id): array;
    public function createRole(CreateRoleRequest $request):array;
    public function updatePermissions(UpdateRoleRequest $request): array;
    public function destroyRole(int $id): array;
}