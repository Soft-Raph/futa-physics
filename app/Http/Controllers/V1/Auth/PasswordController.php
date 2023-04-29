<?php

namespace App\Http\Controllers\V1\Auth;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Auth\ChangePasswordRequest;
use App\Http\Requests\V1\Auth\ForgetPasswordRequest;
use App\Http\Requests\V1\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResources;
use App\Libraries\Redis;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    const FORGET_PASSWORD_MESSAGE = "If your phone number exist in our database, you will receive a password recovery notification at your phone number in a few minutes.";
    public function __construct()
    {
        $this->middleware('auth:api')->only('changePassword');
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        if (! Hash::check($request->password, $user->password)) {
            return ResponseHelper::error('400', 'Incorrect current password');
        }
        if (Hash::check($request->new_password, $user->password)) {
            return ResponseHelper::error('400', 'The password you are supplying is the same, please try to use another password');
        }
        $user->password = bcrypt($request->new_password);
        $user->is_default = 0;
        $user->is_password_changed = true;
        $user->save();

        return ResponseHelper::success($user, 'Password Changed Successfully');
    }

    /**
     * @throws Exception
     */
    public function forgetPassword(ForgetPasswordRequest $request, Redis $redis)
    {
        $phone = $request->phone;
        $user = User::query()->where('phone', $request->phone)
            ->first();
        if (! $user) {
            return ResponseHelper::error('400', 'Account does not exist');
        }
        $reset_code = ResponseHelper::randomCode('6');
        $reset = [
            'phone' => $user->phone,
            'code' => $reset_code,
        ];
        $redis->delete($phone.'-'.$reset_code);
        $redis->putForFiveMinutes($user->phone.'-'.$reset_code, json_encode($reset));
        $message = "Your CICO confirmation code is {$reset_code}. It expires in 2 minutes.";
        //notification service will take the $message to the verified phone number
       dd($message);
        return ResponseHelper::success(null, self::FORGET_PASSWORD_MESSAGE);
    }

    public function resetPassword(ResetPasswordRequest $request, Redis $redis)
    {
        $code = $request->code;
        $phone = $request->phone;
        $password = $request->password;
        $data_from_redis_store = $redis->get($phone.'-'.$code);
        $decoded_data = json_decode($data_from_redis_store);
        if (! $decoded_data) {
            return ResponseHelper::error('400', 'Incorrect or expired code');
        }
        if ($code !== $decoded_data->code) {
            return ResponseHelper::error('400', 'Code mismatch');
        }
        $user = User::query()->where('phone', $decoded_data->phone)->first();
        if (! $user) {
            return ResponseHelper::error('400', 'User not found');
        }
        if (Hash::check($password, $user->password)) {
            return ResponseHelper::error('400', 'The password you are supplying is the same, please try to use another password');
        }
        $user->password = bcrypt($password);
        $user->is_password_changed = true;
        $user->save();
        $redis->delete($phone.'-'.$code);

        return ResponseHelper::success(UserResources::make($user), 'Password Changed Successfully', 201);
    }
}
