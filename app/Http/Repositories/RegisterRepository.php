<?php

namespace App\Http\Repositories;

use App\Http\Interfaces\RegisterInterface;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\InitialRegisterRequest;
use App\Http\Requests\User\Update\UpdatePhoneRequest;
use App\Http\Requests\User\Update\VerifyPhoneRequest;
use App\Models\Headmaster;
use App\Models\Internship_service_member;
use App\Models\Scientific_committee_member;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Notifications\ConfirmRegisterNotification;
use App\Notifications\RegistrationCompletedNotification;
use App\Notifications\SmsNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterRepository implements RegisterInterface
{
    public function __construct(
        private User $user,
        private StudentRepository $stdRepo,
        private TeacherRepository $tchRepo,
        private CommitteeRepository $cmtRepo,
        private InternshipRepository $intRepo,
        private HeadmasterRepository $headRepo
    ){ }

    
    public function createUser(CreateUserRequest $request): array
    {
        try {
            switch ($request->person_type) {
                case $this->user::TYPES["HEADMASTER"]:
                    $user = $this->headRepo->create($request);
                    break;
                case $this->user::TYPES["INTERNSHIP"]:
                    $user = $this->intRepo->create($request);
                    break;
                case $this->user::TYPES["COMMITTEE"]:
                    $user = $this->cmtRepo->create($request);
                    break;
                case $this->user::TYPES["INCUBATOR_PRESIDENT"]:
                    $user = $this->cmtRepo->createPresident($request);
                    break;
            }
            $user->refresh();
            $user->notify(new ConfirmRegisterNotification($user->id, $user->register_verification_hash));
            return ['success' => 1, 'message' => 'user created succesfully', 'user' => $user];
        } catch (\Exception $e) {
            return ['success' => 0, 'message' => 'Error creating user'];
        }
    }

    public function initialRegister(InitialRegisterRequest $request): array
    {
        try {
            switch ($request->person_type) {
                case $this->user::TYPES["STUDENT"]:
                    $user = $this->stdRepo->register($request);
                    break;
                case $this->user::TYPES["TEACHER"]:
                    $user = $this->tchRepo->register($request);
                    break;
            }
            $user->refresh();
            $user->notify(new ConfirmRegisterNotification($user->id, $user->register_verification_hash));
            return ['success' => 1, 'message' => 'user created succesfully', 'user' => $user];
        } catch (\Exception $e) {
            return ['success' => 0, 'message' => 'Error creating user'];
        }
    }

    public function verifyHash($payload): array
    {
        $res = $this->verify($payload);
        if (!$res['success']) {
            return $res;
        }
        $user = $res['user'];
        $references = $this->getReferences($user);
        return [
            'success' => true,
            'message' => 'token validated',
            'user' => $user,
            ...$references
        ];
    }

    public function completeRegister(Request $request, $payload): array
    {
        $res = $this->verify($payload);
        if (!$res['success']) {
            return $res;
        }

        $user = $res['user'];
        switch ($user->person_type) {
            case Student::class:
                $person = $this->stdRepo->completeRegister($request);
                break;
            case Teacher::class:
                $person = $this->tchRepo->completeRegister($request);
                break;
            case Headmaster::class:
                $person = $this->headRepo->completeRegister($request, $user->person_id);
                break;
            case Internship_service_member::class:
                $person = $this->intRepo->completeRegister($request, $user->person_id);
                break;
            case Scientific_committee_member::class:
                $person = $this->cmtRepo->completeRegister($request, $user->person_id);
                break;
        }
        $user->username = $request->username;
        $user->password = $request->password;
        $user->person_id = $person->id;
        $user->register_verification_hash = null;
        if ($request->image) {
            $photo = $user->id . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(public_path('images/users'), $photo);
            $user->photo_url = env('APP_URL') . '/images/users/' . $photo;
        }

        $user->save();
        $user->notify(new RegistrationCompletedNotification());
        $user->refresh();
        return [
            'success' => true,
            'message' => 'User Registred',
            'user' => $user,
        ];
    }

    public function sendVerificationCode(UpdatePhoneRequest $request, $payload): array
    {
        $res = $this->verify($payload);
        if (!$res['success']) {
            return $res;
        }

        $user = $res['user'];
        try {
            $otp = rand(100000, 999999);
            $user->tmp_phone_number = $request->phone_number;
            $user->phone_verif_code = Hash::make($otp);
            $user->phone_verif_code_expires_at = Carbon::now()->addMinutes(5);
            $user->save();
            $message = "StartEase OTP code :" . "$otp" . ". Valid for 5 minutes";
            print_r($otp);
            $user->notify(new SmsNotification($message));
            return [
                'success' => true,
                'message' => 'Verification code sent successfully'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error sending verification code'
            ];
        }
    }

    public function setPhoneNumber(VerifyPhoneRequest $request, $payload): array
    {
        $res = $this->verify($payload);
        if (!$res['success']) {
            return $res;
        }

        $user = $res['user'];
        if (!$user->phone_verif_code) {
            return [
                'success' => false,
                'message' => 'You did not even send a verification'
            ];
        }
        if (($user->phone_verif_code_expires_at) < Carbon::now()) {
            return [
                'success' => false,
                'message' => 'Verification code has been expired'
            ];
        }

        if (!Hash::check($request->verif_code, $user->phone_verif_code)) {
            return [
                'success' => false,
                'message' => 'Verification code is incorrect'
            ];
        }

        $user->phone_number = $user->tmp_phone_number;
        $user->tmp_phone_number = null;
        $user->phone_verif_code = null;
        $user->phone_verif_code_expires_at = null;
        $user->save();
        return [
            'success' => true,
            'message' => 'Phone number set successfully',
            'user' => $user
        ];
    }

    public function getReferences($user)
    {
        switch ($user->person_type) {
            case Student::class:
                return $this->stdRepo->getReferences();
            case Teacher::class:
                return $this->tchRepo->getReferences();
            case Scientific_committee_member::class:
                return $this->cmtRepo->getReferences();
            case Headmaster::class:
                return $this->headRepo->getReferences();
            case Internship_service_member::class:
                return $this->intRepo->getReferences();
        }
    }

    public function verify($payload): array
    {
        [$user_id, $hash] = explode('_', $payload);
        $user = $this->user::find($user_id);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'invalid link'
            ];
        }
        if (!$user->register_verification_hash) {
            return [
                'success' => false,
                'message' => 'you already completed registration'
            ];
        }
        if ($user->register_verification_hash !== $hash) {
            return [
                'success' => false,
                'message' => 'invalid link'
            ];
        }
        return [
            'success' => true,
            'message' => 'token validated',
            'user' => $user,
        ];
    }
}
