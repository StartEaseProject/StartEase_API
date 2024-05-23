<?php

namespace App\Http\Repositories;


use App\Http\Interfaces\UserInterface;
use App\Http\Requests\User\Update\UpdateRolesRequest;
use App\Http\Requests\User\Update\UpdatePasswordRequest;
use App\Http\Requests\User\Update\UpdatePhoneRequest;
use App\Http\Requests\User\Update\UpdatePhotoRequest;
use App\Http\Requests\User\Update\VerifyPhoneRequest;
use App\Models\ProjectMember;
use App\Models\User;
use App\Notifications\SmsNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Hash;

/**
 * Summary of UserRepository
 */
class UserRepository implements UserInterface
{
    public function __construct(
        private User $user,
        private ProjectMember $project_member
    ) {
    }

    
    public function all(): array
    {
        try {
            $users = $this->user::whereNotNull('username')->get();
            return [
                'success' => true,
                'message' => 'Users retreived successfully',
                'users' => $users,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Could not retreive users",
            ];
        }
    }

    public function getById($id): array
    {
        $user = $this->user::find($id);
        if (!$user) return ['success' => 0, 'message' => 'user not found'];

        return ['success' => 1, 'message' => 'user retreived successfully', "user" => $user];
    }

    public function rolesList($id): array
    {
        $user = $this->user::find($id);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found'
            ];
        }

        return ['success' => 1, 'message' => 'roles retreived', 'roles' => $user->roles];
    }

    public function enableUser($id)
    {
        $user = $this->user::find($id);

        if (!$user) return ['success' => 0, 'message' => 'user not found'];

        if (!$user->is_enabled) {
            $user->is_enabled = 1;
            $user->save();
            return ['success' => 1, 'message' => 'user enabled successfully', 'user' => $user];
        }
        return ['success' => 0, 'message' => 'user already enabled'];
    }

    public function disableUser($id)
    {
        $user = $this->user::find($id);
        if (!$user) return ['success' => 0, 'message' => 'user not found'];

        if ($user->is_enabled) {
            $user->is_enabled = 0;
            $user->save();
            return ['success' => 1, 'message' => 'user disabled successfully', 'user' => $user];
        }

        return ['success' => 0, 'message' => 'user already disabled', 'user' => $user];
    }

    public function updateAuthPassword(UpdatePasswordRequest $request): array
    {

        $current_password = $request->current_password;
        $new_password = $request->new_password;

        $user = $this->user::find(auth('sanctum')->id());
        if (!Hash::check($current_password, $user->password)) {
            return [
                'success' => false,
                'message' => 'Wrong password'
            ];
        }

        $user->password = $new_password;
        $user->save();
        return [
            'success' => true,
            'message' => 'Password Changed Successfully'
        ];
    }

    public function updateAuthPhoto(UpdatePhotoRequest $request): array
    {
        $user = $this->user::find(auth('sanctum')->id());
        $photo = $user->id . '.' . $request->image->getClientOriginalExtension();

        $request->image->move(public_path('images/users'), $photo);

        $user->photo_url = env('APP_URL') . '/images/users/' . $photo;

        try {
            $user->save();
        } catch (Exception $e) {
            return ['success' => 0, 'message' => 'error updating photo'];
        }

        return ['success' => 1, 'message' => 'photo updated', 'user' => $user];
    }

    public function sendVerificationCode(UpdatePhoneRequest $request): array
    {
        $user = $this->user::find(auth('sanctum')->id());

        try {
            $otp = rand(100000, 999999);
            $user->tmp_phone_number = $request->phone_number;
            $user->phone_verif_code = Hash::make($otp);
            $user->phone_verif_code_expires_at = Carbon::now()->addMinutes(5);
            $user->save();
            $message = "StartEase OTP code :" . "$otp" . ". Valid for 5 minutes";
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

    public function updateAuthPhoneNumber(VerifyPhoneRequest $request): array
    {
        $user = $this->user::find(auth('sanctum')->id());
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
            'message' => 'Phone number updated successfully',
            'user' => $user
        ];
    }

    public function updateUserRoles(UpdateRolesRequest $request): array
    {
        $user = $this->user::find($request['user']);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'user not found'
            ];
        }

        try {
            $user->roles()->sync($request['roles']);
            return ['success' => 1, 'message' => 'Role updated', 'user' => $user];
        } catch (Exception $e) {
            return ['success' => 0, 'message' => 'error updating user'];
        }
    }
}
