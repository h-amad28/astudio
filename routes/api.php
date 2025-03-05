<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AttributeValueController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\TimeSheetsController;

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

Route::post('register', [UsersController::class, 'store']);
Route::post('login', [UsersController::class, 'login']);

Route::middleware('auth:api')->group(function() {
    Route::get('logout', [UsersController::class, 'logout']);
    Route::put('project/user', [ProjectController::class, 'assignUser']);

    Route::apiResources([
        'project' => ProjectController::class,
        'attributes' => AttributeController::class,
        'attribute-values' => AttributeValueController::class,
        'time-sheet' => TimeSheetsController::class,
    ]);

});
