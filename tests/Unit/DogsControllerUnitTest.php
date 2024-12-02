<?php

namespace Tests\Unit;

use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\User;
use App\Models\Dog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DogsControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    public function testDogList()
    {
        Dog::factory()->count(3)->create(['is_public' => true]);
        Dog::factory()->create(['is_public' => false]);

        $response = $this->get('/api/dog_list');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'breed',
                    'gender',
                    'is_public',
                    'img_path',
                    'user_id',
                    'created_at',
                    'updated_at',
                ]
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'links',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total',
        ]);
    }

    public function testDogListStore()
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'Rex',
            'breed' => 'Labrador',
            'gender' => 'M',
            'is_public' => true,
        ];


        $this->actingAs($user);
        $response = $this->post('/api/dog_list/store', $data);
        $response->assertStatus(200);


        $this->assertDatabaseHas('dogs', [
            'name' => 'Rex',
            'breed' => 'Labrador',
            'gender' => 'M',
            'is_public' => true,
            'img_path' => null,
        ]);
    }

    public function testCurrentDog()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $dog = Dog::factory()->create(['user_id' => $user->id]);

        $response = $this->get('/api/dog_list/current_dog/' . $dog->id);
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $dog->id,
            'name' => $dog->name,
            'breed' => $dog->breed,
            'gender' => $dog->gender,
        ]);
    }

    public function testDogListUpdate()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $dog = Dog::factory()->create(['user_id' => $user->id]);

        $updatedData = [
            'name' => 'Novo Nome',
            'breed' => 'Nova Raça',
            'gender' => 'F',
            'is_public' => false,
        ];

        $response = $this->put('/api/dog_list/update/' . $dog->id, $updatedData);
        $response->assertStatus(200);

        $this->assertDatabaseHas('dogs', [
            'id' => $dog->id,
            'name' => 'Novo Nome',
            'breed' => 'Nova Raça',
            'gender' => 'F',
            'is_public' => false,
        ]);
    }

    public function testDogListDestroy()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $dog = Dog::factory()->create(['user_id' => $user->id]);

        $response = $this->delete('/api/dog_list/delete/' . $dog->id);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('dogs', ['id' => $dog->id]);
    }

    public function testDeleteImage()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $dog = Dog::factory()->create(['user_id' => $user->id]);

        $response = $this->put('/api/dog_list/delete_image/' . $dog->id);
        $response->assertStatus(200);
        $this->assertDatabaseHas('dogs', [
            'id' => $dog->id,
            'img_path' => null
        ]);

        $this->assertFileDoesNotExist(storage_path("app/public/images/$user->id/$dog->img_path"));
    }
}
