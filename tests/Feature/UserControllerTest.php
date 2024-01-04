<?php

namespace Tests\Unit\Controllers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory; // Import FakerFactory
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test the store method.
     */
    public function testCreateUser()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        // Create a Faker instance
        $faker = FakerFactory::create();

        Storage::fake('public');

        // Generate random avatar image using Faker
        $avatar = UploadedFile::fake()->image('avatar.jpg');

        $data = [
            'first_name' => $faker->firstName,
            'last_name' => $faker->lastName,
            'username' => $faker->userName,
            'email' => $faker->unique()->safeEmail,
            'password' => 'password123',
            'store_name' => $faker->words(2, true),
            'avatar_url' => $avatar,
        ];

        $response = $this->post('/api/register', $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['email' => $data['email']]);

        Storage::disk('public')->assertExists('images/' . $avatar->hashName());
    }

    // Rest of the test methods...
}
