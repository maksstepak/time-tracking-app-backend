<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthenticationController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthenticationController::class, 'getCurrentUser']);
    Route::post('/logout', [AuthenticationController::class, 'logout']);

    Route::apiResource('users', UserController::class);
    Route::get('/clients/select-options', [ClientController::class, 'getSelectOptions']);
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('projects', ProjectController::class)->except(['store']);
    Route::post('/clients/{client}/projects', [ProjectController::class, 'store']);
    Route::post('/projects/{project}/assign-users', [ProjectController::class, 'assignUsers']);
    Route::post('/projects/{project}/remove-users', [ProjectController::class, 'removeUsers']);
});
