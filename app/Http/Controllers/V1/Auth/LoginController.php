<?php

namespace App\Http\Controllers\V1\Auth;

use App\Helpers\ResponseHelper;
use App\Http\Requests\V1\Auth\LoginRequest;
use App\Http\Resources\UserResources;
use App\Models\User;
use App\Traits\AccessTokenTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;

class LoginController extends Controller
{
    use AccessTokenTrait;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = ['matric_number' => $request->matric_number, 'password' => $request->password];

            if (! Auth::attempt($credentials)) {
                return ResponseHelper::error(401, 'Invalid login credentials');
            }
            $user = $request->user();

            if ($request->get('remember_me')) {
                Passport::personalAccessTokensExpireIn(now()->addDays(30));
            }
//            $user->last_login = Carbon::now();
            $user->save();
            $personalAccessToken = Auth::user()->createToken('Personal Access Token');
            $expires_at = Carbon::parse($personalAccessToken->token->expires_at)->addHours(2)->toDateTimeString();
            $user_data =UserResources::make($request->user());

            return $this->token($personalAccessToken, $expires_at, $user_data);
        } catch (\Exception $e) {
            return ResponseHelper::error(500, $e->getMessage());
        }
    }
}
