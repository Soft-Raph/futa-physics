<?php

namespace App\Http\Controllers\V1\Auth;

use App\Helpers\ResponseHelper;
use App\Http\Resources\ApiKeyResource;
use App\Models\ApiKey;
use App\Traits\AccessTokenTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Passport\Passport;

class ApiKeyController extends Controller
{
    use AccessTokenTrait;

    public function __construct()
    {
        $this->middleware('auth:api')->only(['index', 'destroy']);
        $this->middleware('guest')->only('keyacess');
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $userKeys = $request->user()->agent->apiKey;

            if (is_null($userKeys)) {
                return ResponseHelper::error(401, 'An error occur please check your details');
            }

            return ResponseHelper::success(ApiKeyResource::make($userKeys), 'Api keys fetched successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error(500, $e->getMessage());
        }
    }

    public function destroy(ApiKey $apiKey): JsonResponse
    {
        $apiKey->delete();

        return ResponseHelper::success(null, 'Api key deleted successfully');
    }

    public function keyacess(Request $request)
    {
        try {
            $supplied_key = $request->api_key;
            $check_key = ApiKey::where('api_key', $supplied_key)->first();
            if (! $check_key) {
                return ResponseHelper::error(401, 'Access denied, check the API key used');
            }
            $get_namespace = $check_key->resource_namespace;
            $get_id = $check_key->resource_id;
            $get_name = $check_key->resource_name;
            $agent = $get_namespace::where('id', $get_id)->first();
            if (! $agent) {
                return ResponseHelper::error(401, 'No '.$get_name.' with this access key');
            }
            $user = $agent->user;
            if (! $user) {
                return ResponseHelper::error(401, 'This '.$get_name.' is not registered');
            }
            $personalAccessToken = $user->createToken('Personal Access Token');
            $token = $personalAccessToken->token;
            if ($request->get('remember_me')) {
                Passport::personalAccessTokensExpireIn(now()->addDays(30));
            }
            $token->save();
            $expires_at = Carbon::parse($personalAccessToken->token->expires_at)->addMonths(6)->toDateTimeString();

            return $this->token($personalAccessToken, $expires_at);
        } catch (\Exception $e) {
            return ResponseHelper::error(500, $e->getMessage());
        }
    }
}
