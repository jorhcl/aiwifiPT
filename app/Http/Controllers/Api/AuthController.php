<?php

/**
 *
 * @author. Jorge Cortes Lopez
 *
 * Class   AuthController
 *   Controller to  client register and authentication  to add logic in authentication flow
 *
 *
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ClientLoginRequest;
use App\Http\Requests\Client\ClientRegisterRequest;
use App\Http\Resources\ClientResource;
use App\Services\ClientAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //

    protected ClientAuthService $clientAuthService;

    public function __construct(ClientAuthService $service)
    {
        $this->clientAuthService = $service;
    }

    /**
     *
     *  Register method
     *
     *  @param ClientRegisterRequest $request
     *
     *  @return JsonResponse ['client', 'token]
     *
     */

    public function register(ClientRegisterRequest $request): JsonResponse
    {
        $data = $this->clientAuthService->register($request->validated());

        return response()->json([
            'client' => new ClientResource($data['client']),
            'token' => $data['token'],
        ], 201);
    }

    /**
     *
     *  Login  method
     *
     *  @param ClientLoginRequest $request
     *
     *  @return JsonResponse ['client', 'token]
     *
     */

    public function login(ClientLoginRequest $request): JsonResponse
    {
        $data = $this->clientAuthService->login($request->validated());

        return response()->json([
            'client' => new ClientResource($data['client']),
            'token' => $data['token'],
        ]);
    }


    /**
     *
     *  Logout  method
     *
     *  @param Request $request
     *
     *  @return JsonResponse ['message']
     *
     */

    public function logout(Request $request): JsonResponse
    {
        $this->clientAuthService->logout($request->user());

        return response()->json(['message' => 'Session closed successfully']);
    }



    /**
     *
     *  Profile method
     *
     *  @param Request $request
     *  @return JsonResponse ['client']
     *
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'client' => new ClientResource($request->user())
        ]);
    }
}
