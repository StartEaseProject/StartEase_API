<?php

namespace App\Http\Interfaces;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\InitialRegisterRequest;
use App\Http\Requests\User\Update\UpdatePhoneRequest;
use App\Http\Requests\User\Update\VerifyPhoneRequest;
use Illuminate\Http\Request;

interface RegisterInterface
{
    public function createUser(CreateUserRequest $request) : array;
    public function initialRegister(InitialRegisterRequest $request):array;
    public function verifyHash($payload) : array;
    public function completeRegister(Request $request,  $payload): array;
    public function sendVerificationCode(UpdatePhoneRequest $request,  $payload): array;
    public function setPhoneNumber(VerifyPhoneRequest $request,  $payload): array;
}