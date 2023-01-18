<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function test_returns_access_token_on_correct_credentials()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => $json->has('accessToken'));
    }

    public function test_returns_wrong_credentials_error_on_incorrect_password()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response
            ->assertStatus(400)
            ->assertJson([
                'error' => 'wrongCredentials',
            ]);
    }

    public function test_returns_user_when_logged_in()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/me');

        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    public function test_returns_unauthorized_when_not_logged_in()
    {
        $response = $this->getJson('/api/me');

        $response->assertUnauthorized();
    }
}
