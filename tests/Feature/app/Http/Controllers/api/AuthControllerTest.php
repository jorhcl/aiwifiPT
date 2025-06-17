<?php

namespace Tests\Feature\app\Http\Controllers\api;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  Test  to client register
     */
    public function test_client_can_register(): void
    {

        $response = $this->postJson(route('client.register'), [
            'name' => 'Jorge Cortes',
            'email' => 'test@test.com',
            'password' => '12345678',

        ]);

        $response->assertCreated()->assertJsonStructure([
            'client' => ['id', 'name', 'email', 'created_at'],
            'token'
        ]);
        $this->assertDatabaseHas('clients', ['email' => 'test@test.com']);
    }


    /**
     *  Test to check  valid register fields
     *
     */
    public function test_invalid_data_to_register(): void
    {
        $response = $this->postJson(route('client.register'), [
            'name' => '',
            'email' => 'no-es-email',
            'password' => '123',

        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /**
     *
     *  Test to login
     *
     */

    public function test_client_login(): void
    {

        Client::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('12345678')
        ]);

        $response = $this->postJson(route('client.login'), [
            'email' => 'test@example.com',
            'password' => '12345678',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'client' => ['id', 'name', 'email', 'created_at'],
                'token'
            ]);
    }

    /**
     *
     *  Test to valid fail login
     *
     *
     */

    public function test_client_login_fail(): void
    {

        Client::factory()->create([
            'email' => 'juan@example.com',
            'password' => Hash::make('12345678')
        ]);

        $response = $this->postJson(route('client.login'), [
            'email' => 'juan@example.com',
            'password' => 'notValid',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['error']);
    }


    /**
     *
     *  Test get profile
     *
     */

    public function test_client_get_profile()
    {
        $client = Client::factory()->create();
        $token = $client->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson(route('client.get.profile'));

        $response->assertOk()
            ->assertJsonStructure([
                'client' => ['id', 'name', 'email']
            ]);
    }


    /**
     *
     *  Test client logout
     *
     */

    public function test_client_can_logout()
    {
        $client = Client::factory()->create();
        $token = $client->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson(route('client.logout'));

        $response->assertOk()
            ->assertExactJson(['message' => 'Session closed successfully']);
    }
}
