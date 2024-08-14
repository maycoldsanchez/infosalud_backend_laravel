<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::prefix('users')->group(function () {
    Route::get('/', [UsersController::class, 'getUsers']);
    Route::get('/table', [UsersController::class, 'getUsersTable']);
    Route::post('/', [UsersController::class, 'storeUser']);
    Route::patch('/', [UsersController::class, 'updateUser']);
    Route::delete('/', [UsersController::class, 'deleteUser']);
});

Route::middleware(['auth', 'second'])->group(function () {

});
