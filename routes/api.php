<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\kisahnesiaController;
use App\Http\Controllers\Api\V1\tagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// user
Route::post('v1/auth/login', [AuthController::class, 'login']);
Route::post('v1/auth/register', [AuthController::class, 'register']);
Route::post('v1/auth/logout', [AuthController::class, 'logout']);
Route::post('v1/auth/refresh', [AuthController::class, 'refresh']);
Route::post('v1/auth/me', [AuthController::class, 'me']);

Route::controller(kisahnesiaController::class)->group(function(){
    Route::get('v1/kisahnesia/stories', 'allStory');
    Route::get('v1/kisahnesia/story/{slug}', 'aStory');
    Route::post('v1/kisahnesia/create', 'newStory')->middleware('jwt.auth');
});

Route::controller(tagController::class)->group(function(){
    Route::get('v1/tags', 'allTags');
});