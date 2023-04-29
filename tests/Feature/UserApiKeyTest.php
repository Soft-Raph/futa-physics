<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class UserApiKeyTest extends TestCase
{
    private function jsonResponse(string $status, string $message, $data = null): array
    {
        return [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];
    }

    public function test_a_user_can_fetch_api_key()
    {
        Artisan::call('passport:install');
        $user = User::factory()->create();
        $agent = Agent::factory()->create(['user_id' => $user->id]);
        $apikey = ApiKey::factory()->create(['resource_id' => $agent->id]);
        $userkey = $user->agent->apikey;

        $data = [
            'api_key' => $userkey->api_key,
            'api_secret' => $userkey->api_secret,
        ];
        $token = $user->createToken('Personal Access Token');
        $access_token = $token->accessToken;
        $response = $this->get(route('api_keys'), [
            'Authorization' => 'Bearer '.$access_token,
        ]);
        $response->assertStatus(200);
        $response->assertJson($this->jsonResponse(
            'success',
            'Api keys fetched successfully',
            $data
        ));
        $apikey->delete();
        $agent->delete();
        $user->delete();
    }

    public function test_a_user_can_delete_api_key()
    {
        Artisan::call('passport:install');
        $user = User::factory()->create();
        $agent = Agent::factory()->create(['user_id' => $user->id]);
        $token = $user->createToken('Personal Access Token');
        $access_token = $token->accessToken;
        $api_key = ApiKey::factory()->create(['resource_id' => $agent->id]);
        $response = $this->delete(route('api_keys.destroy', $api_key->uuid), [], [
            'Authorization' => 'Bearer '.$access_token,
        ]);
        $response->assertStatus(200);
        $response->assertJson($this->jsonResponse(
            'success',
            'Api key deleted successfully'
        ));
        $api_key->delete();
        $agent->delete();
        $user->delete();
    }

    public function test_a_user_can_login_with_key()
    {
        Artisan::call('passport:install');
        $user = User::factory()->create();
        $agent = Agent::factory()->create(['user_id' => $user->id]);
        $apikey = ApiKey::factory()->create(['resource_id' => $agent->id]);
        $userkey = $user->agent->apikey;
        $response = $this->get(route('auth.key', [
            'api_key' => $userkey->api_key,
        ]));
        $this->assertGuest();
        $user->createToken('Personal Access Token');
        $response->assertStatus(200);
        $data = [
        ];
        $response->assertJson($this->jsonResponse(
            'success',
            'User logged in successfully',
             $data
        ));
        $apikey->delete();
        $agent->delete();
        $user->delete();
    }

    public function test_a_user_cannot_login_with_wrong_key()
    {
        Artisan::call('passport:install');
        $user = User::factory()->create();
        $response = $this->get(route('auth.key', [
            'api_key' => 'wrongapikey',
        ]));
        $this->assertGuest();
        $user->createToken('Personal Access Token');
        $response->assertStatus(401);
        $data = [
        ];
        $response->assertJson($this->jsonResponse(
            'error',
            'Access denied, check the API key used',
            $data
        ));
        $user->delete();
    }
}
