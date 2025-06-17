<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('client')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('client.register');
    Route::post('login', [AuthController::class, 'login'])->name('client.login');

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('profile', [AuthController::class, 'profile'])->name('client.get.profile');
        Route::post('logout', [AuthController::class, 'logout'])->name('client.logout');
    });
});


Route::prefix('contacts')->middleware('auth:sanctum')->group(function () {

    Route::post('/upload', [ContactController::class, 'uploadContacts'])->name('contact.upload.cvs');
    Route::get('/', [ContactController::class, 'index'])->name('contact.get.list');
    Route::delete('/{id}', [ContactController::class, 'destroy'])->name('contact.delete');
});
