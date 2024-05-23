<?php

namespace App\Http\Interfaces;

use App\Http\Requests\User\Update\UpdateRolesRequest;
use App\Http\Requests\User\Update\UpdatePasswordRequest;
use App\Http\Requests\User\Update\UpdatePhoneRequest;
use App\Http\Requests\User\Update\UpdatePhotoRequest;
use App\Http\Requests\User\Update\VerifyPhoneRequest;

interface UserInterface
{
    public function all():array;
    public function getById($id) : array;
    public function rolesList($id) : array;
    public function enableUser($id);
    public function disableUser($id);
    public function updateAuthPassword(UpdatePasswordRequest $request) : array;
    public function updateAuthPhoto(UpdatePhotoRequest $request) : array;
    public function sendVerificationCode(UpdatePhoneRequest $request):array;
    public function updateAuthPhoneNumber(VerifyPhoneRequest $request):array;
    public function updateUserRoles(UpdateRolesRequest $request):array;
}