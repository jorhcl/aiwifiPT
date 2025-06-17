<?php

namespace App\Providers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    public function exceptions(): void
    {
        $this->renderable(function (AuthenticationException $e, Request $request) {
            return Response::json([
                'message' => 'Unauthenticated.'
            ], 401);
        });
    }
}
