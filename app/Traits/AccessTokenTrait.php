<?php

namespace App\Traits;

use App\Helpers\ResponseHelper;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

trait AccessTokenTrait
{
    protected function token($personalAccessToken, $expires_at, $user_data): JsonResponse
    {
        $tokenData = [
            'access_token' => $personalAccessToken->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $expires_at,
            'user_data' => $user_data,
            'created_at' => Carbon::parse($personalAccessToken->token->created_at)->toDateTimeString(),
        ];

        return ResponseHelper::success($tokenData, 'User logged in successfully');
    }
}
