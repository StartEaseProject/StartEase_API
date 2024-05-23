<?php

namespace App\Http\Interfaces;
use App\Http\Requests\Permission\CreatePermissionRequest;



interface PermissionInterface
{   
    public function all():array;
    public function createPermission(CreatePermissionRequest $request) : array;
    public function destroyPermission($id) : array;
}