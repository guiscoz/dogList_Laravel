<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testRegister()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $response = $this->json('POST', '/api/register', $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'user',
            'token'
        ]);

        $createdUser = $response->json('user');

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
    }

    public function testLogin()
    {
        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        User::factory()->create([
            'email' => $credentials['email'],
            'password' => bcrypt($credentials['password']),
        ]);

        $response = $this->postJson('/api/login', $credentials);

        $response->assertStatus(201);
        $response->assertJsonStructure(['token']);

        $credentials['password'] = 'incorrectpassword';
        $response = $this->postJson('/api/login', $credentials);
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Credenciais invalidas']);

        $credentials['email'] = 'nonexistent@example.com';
        $response = $this->postJson('/api/login', $credentials);
        $response->assertStatus(401);
        $response->assertJson(['message' => 'Credenciais invalidas']);
    }

    public function testGetUser()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/api/get_user');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'user' => [
                'id',
                'name',
                'email',
            ],
            'dogs' => [],
        ]);

        $this->assertEquals($user->id, $response->json('user.id'));
        $this->assertEquals($user->name, $response->json('user.name'));
        $this->assertEquals($user->email, $response->json('user.email'));
    }

    public function testLogout()
    {
        $user = User::factory()->create();

        $token = $user->createToken('Test Token')->plainTextToken;

        $this->actingAs($user);

        $response = $this->get('/api/logout');

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Logout efetuado com sucesso e exclusão dos tokens.'
        ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'token' => $token
        ]);
    }
}
