<?php


/**
 *
 * @author. Jorge Cortes Lopez
 *
 * Class   ClientAuthService
 *   Service to add logic in authentication flow
 *
 *
 */


namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ClientAuthService
{

    /**
     *
     *  Client register method
     *
     *  @param  Array $data
     *  @return array ['client','token']
     *
     *
     */
    public function register(array $data): array
    {

        $data['password'] = Hash::make($data['password']);
        $client = Client::create($data);
        $token = $client->createToken('client_token')->plainTextToken;

        return ['client' => $client, 'token' => $token];
    }


    /**
     *
     *  Client login method
     *
     *  @param  Array $credentials
     *  @return array ['client','token']
     *
     *
     */
    public function login(array $credentials): array
    {
        $client = Client::where('email', $credentials['email'])->first();

        if (!$client || !Hash::check($credentials['password'], $client->password)) {
            throw ValidationException::withMessages([
                'error' => ['The credentials are not valid.'],
            ]);
        }

        $token = $client->createToken('client_token')->plainTextToken;

        return ['client' => $client, 'token' => $token];
    }


    /**
     *
     *  Client logout method
     *
     *  @param  Client $client
     *
     *
     *
     */
    public function logout(Client $client): void
    {
        $token = $client->currentAccessToken();
        if ($token) {
            $token->delete();
        }
    }
}
