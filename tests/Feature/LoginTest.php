<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class LoginTest extends TestCase
{
    private function jsonResponse(string $status, string $message, $data = null): array
    {
        return [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];
    }

    public function test_a_user_cannot_login_with_invalid_data()
    {
        $user = User::factory()->create();
        $response = $this->post(route('login'), [
            'username' => $user->username,
            'password' => 'wrong-password',
        ]);
        $this->assertGuest();
        $response->assertStatus(401);
        $response->assertJson($this->jsonResponse(
            'error',
            'Invalid login credentials',
            null
        ));
        $user->delete();
    }

    public function test_a_user_can_login()
    {
        Artisan::call('passport:install');
        $user = User::factory()->create();
        $response = $this->post(route('login'), [
            'username' => $user->username,
            'password' => 'password',
        ]);
        $this->assertAuthenticated();
        $user->createToken('Personal Access Token');
        $response->assertStatus(200);
        $response->assertJson($this->jsonResponse(
            'success',
            'User logged in successfully',
            []
        ));
        $user->delete();
    }
}
