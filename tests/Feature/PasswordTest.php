<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PasswordTest extends TestCase
{
    private function jsonResponse(string $status, string $message, $data = null): array
    {
        return [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_the_password_field_is_require(): void
    {
        Artisan::call('passport:install');
        $user = User::factory()->create();
        $token = $user->createToken('Personal Access Token');
        $access_token = $token->accessToken;
        $response = $this->get(route('change.password'), [
            'Authorization' => 'Bearer '.$access_token,
        ]);
        $response->assertStatus(422);
        $response->assertJson($this->jsonResponse(
               'error',
               'The password field is required.',
           ));
        $user->delete();
    }

    public function test_a_user_can_change_password(): void
    {
        $require = [
            'password' => 'password',
            'new_password' => 'changepass',
            'confirm_password' => 'changepass',
        ];
        Artisan::call('passport:install');
        $user = User::factory()->create();
        $data = [
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone,
            'is_password_changed' => $user->is_password_changed,
        ];
        $token = $user->createToken('Personal Access Token');
        $access_token = $token->accessToken;
        $response = $this->get(route('change.password', $require), [
            'Authorization' => 'Bearer '.$access_token,
        ]);
        $response->assertStatus(200);
        $response->assertJson($this->jsonResponse(
               'success',
               'Password Changed Successfully',
               $data
           ));
        $user->delete();
    }

    public function test_a_user_cannot_change_password_to_the_previous_password(): void
    {
        $require = [
            'password' => 'password',
            'new_password' => 'password',
            'confirm_password' => 'password',
        ];
        Artisan::call('passport:install');
        $user = User::factory()->create();
        $token = $user->createToken('Personal Access Token');
        $access_token = $token->accessToken;
        $response = $this->get(route('change.password', $require), [
            'Authorization' => 'Bearer '.$access_token,
        ]);
        $response->assertStatus(422);
        $response->assertJson($this->jsonResponse(
               'error',
               'The password you are supplying is the same, please try to use another password',
           ));
        $user->delete();
    }

    public function test_the_confirm_password_and_the_new_password_must_match(): void
    {
        $require = [
            'password' => 'password',
            'new_password' => 'password',
            'confirm_password' => 'change',
        ];
        Artisan::call('passport:install');
        $user = User::factory()->create();
        $token = $user->createToken('Personal Access Token');
        $access_token = $token->accessToken;
        $response = $this->get(route('change.password', $require), [
            'Authorization' => 'Bearer '.$access_token,
        ]);
        $response->assertStatus(422);
        $response->assertJson($this->jsonResponse(
               'error',
               'The confirm password and new password must match.',
           ));
        $user->delete();
    }

    public function test_a_user_cannot_forget_password(): void
    {
        $require = [
            'phone' => '79707070',
        ];
        Artisan::call('passport:install');
        $user = User::factory()->create();
        $token = $user->createToken('Personal Access Token');
        $access_token = $token->accessToken;
        $response = $this->post(route('forget.password', $require), [
            'Authorization' => 'Bearer '.$access_token,
        ]);
        $response->assertStatus(403);
        $response->assertJson($this->jsonResponse(
            'error',
            'Account does not exist',
        ));
        $user->delete();
    }

    public function test_a_user_can_forget_password(): void
    {
        Artisan::call('passport:install');
        $user = User::factory()->create();
        $token = $user->createToken('Personal Access Token');
        $access_token = $token->accessToken;
        $require = [
            'phone' => $user->phone,
        ];
        $response = $this->post(route('forget.password', $require), [
            'Authorization' => 'Bearer '.$access_token,
        ]);
        $response->assertStatus(200);
        $response->assertJson($this->jsonResponse(
            'success',
            'If your phone number exist in our database, you will receive a password recovery notification at your phone number in a few minutes.',
        ));
        $user->delete();
    }

    public function test_a_user_cannot_reset_password_with_wrong_otp(): void
    {
        $require = [
            'password' => 'changepass',
            'code' => '1122',
            'confirm_password' => 'changepass',
        ];
        Artisan::call('passport:install');
        $user = User::factory()->create();
        $token = $user->createToken('Personal Access Token');
        $access_token = $token->accessToken;
        $response = $this->post(route('reset.password', $require), [
            'Authorization' => 'Bearer '.$access_token,
        ]);
        $response->assertStatus(400);
        $response->assertJson($this->jsonResponse(
            'error',
            'Incorrect or expired code',
        ));
        $user->delete();
    }
}
