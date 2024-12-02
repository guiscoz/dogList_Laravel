<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
class AuthControllerFeatureTest extends TestCase
{
    use RefreshDatabase;
    private User $user;
    private string $password;

    protected function setUp(): void
    {
        parent::setUp();

        $this->password = 'password123';

        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt($this->password ),
        ]);
    }

    public function testRegisterWithSuccess()
    {
        $data = [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password123'
        ];

        $response = $this->json('POST', '/api/register', $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'user',
            'token'
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'new@example.com'
        ]);
    }

    public function testRegisterWithDuplicateEmail()
    {
        $data = [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => $this->password
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function testRegisterWithMissingFields()
    {
        $data = [
            'name' => 'New User'
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422); 
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    public function testLoginWithValidCredentials()
    {
        $credentials = [
            'email' => $this->user->email,
            'password' => $this->password,
        ];

        $response = $this->postJson('/api/login', $credentials);

        $response->assertStatus(201);
        $response->assertJsonStructure(['token']);
    }

    public function testLoginWithInvalidPassword()
    {
        $credentials = [
            'email' => $this->user->email,
            'password' => 'wrongpassword',
        ];

        $response = $this->postJson('/api/login', $credentials);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Credenciais invalidas']);
    }

    public function testLoginWithNonExistentEmail()
    {
        $credentials = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $credentials);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Credenciais invalidas']);
    }

    public function testGetAuthenticatedUser()
    {
        $this->actingAs($this->user);

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

        $this->assertEquals($this->user->id, $response->json('user.id'));
        $this->assertEquals($this->user->name, $response->json('user.name'));
        $this->assertEquals($this->user->email, $response->json('user.email'));
    }

    public function testGetUnauthenticatedUser()
    {
        $response = $this->get('/api/get_user');

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    public function testGetUserWithoutDogs()
    {
        $this->actingAs($this->user);

        $response = $this->get('/api/get_user');

        $response->assertStatus(200);
        $response->assertJson([
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'dogs' => [],
        ]);
    }

    public function testLogout()
    {
        $token = $this->user->createToken('Test Token')->plainTextToken;

        $this->actingAs($this->user);

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
