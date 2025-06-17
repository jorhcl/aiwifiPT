<?php

namespace Tests\Feature\app\Http\Controllers\api;

use App\Models\Client;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{

    use RefreshDatabase;
    /**
     *
     *  Test to valid client can upload csv file
     *
     */
    public function test_authenticated_user_can_upload_csv_file()
    {
        Storage::fake('local');
        Queue::fake();

        $user = Client::factory()->create();
        Sanctum::actingAs($user);

        $csv = UploadedFile::fake()->createWithContent('contacts.csv', "name,email\nJuan Lopez,juan@mail.com");

        $response = $this->postJson(route('contact.upload.cvs'), [
            'file' => $csv,
        ]);


        $response->assertAccepted()
            ->assertJson([
                'message' => 'Csv file loaded successfully'
            ]);
    }

    /**
     *
     *  Test to validate that unauthenticated  client can not upload list
     *
     */

    public function test_unauthenticated_client_can_not_upload()
    {
        $response = $this->postJson(route('contact.upload.cvs'), []);
        $response->assertUnauthorized();
    }


    /**
     *
     *  Test to list client contacts
     *
     */

    public function test_client_can_list_own_contacts()
    {
        $client = Client::factory()->create();
        Contact::factory()->count(3)->create(['client_id' => $client->id]);

        Sanctum::actingAs($client);

        $response = $this->getJson(route('contact.get.list'));

        $response->assertOk()
            ->assertJsonStructure([
                'contacts'
            ]);
    }


    /**
     *
     *  Test to valid user can delete own contacts
     *
     */


    public function test_client_can_delete_own_contact()
    {
        $client = Client::factory()->create();
        $contact = Contact::factory()->create([
            'client_id' => $client->id,
        ]);

        Sanctum::actingAs($client);

        $response = $this->deleteJson(route('contact.delete', ['id' => $contact->id]));


        $response->assertOk()
            ->assertJson([
                'message' => 'Contact deleted successfully'
            ]);

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }


    /**
     *
     *  Test to valid user cannot delete other client contact
     *
     */


    public function test_client_can_not_delete_other_client_contact()
    {
        $client = Client::factory()->create();
        $otherUser = Client::factory()->create();
        $contact = Contact::factory()->create([
            'client_id' => $otherUser->id,
        ]);

        Sanctum::actingAs($client);

        $response = $this->deleteJson(route('contact.delete', ['id' => $contact->id]));

        $response->assertStatus(404);
    }
}
