<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PersonController;
use Illuminate\Http\Request;
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

// API route for register new user
Route::post('/register', [AuthController::class, 'register']);
// API route for login user
Route::post('/login', [AuthController::class, 'login']);

// Protecting Routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/profile', function(Request $request) {
        return auth()->user();
    });

    // route for get all person
    Route::get('/person', [PersonController::class, 'index']);
    // route for get single person
    Route::get('/person/{id}', [PersonController::class, 'get']);
    // route for create person
    Route::post('/person', [PersonController::class, 'store']);
    // route for update person
    Route::put('/person/{id}', [PersonController::class, 'update']);
    // route for delete person
    Route::delete('/person/{id}', [PersonController::class, 'destroy']);

    // route for get all patients
    Route::get('/patients', [PatientController::class, 'index']);
    // route for get single patient
    Route::get('/patients/{id}', [PatientController::class, 'get']);
    // route for create patient
    Route::post('/patients', [PatientController::class, 'store']);
    // route for update patient
    Route::put('/patients/{id}', [PatientController::class, 'update']);
    // route for delete patient
    Route::delete('/patients/{id}', [PatientController::class, 'destroy']);

    // route for get patient by person name
    Route::get('/patients/search/{name}', [PatientController::class, 'search']);
    // route for get patient by status = positive
    Route::get('/patients/status/positive', [PatientController::class, 'searchPositive']);
    // route for get patient by status = recovered
    Route::get('/patients/status/recovered', [PatientController::class, 'searchRecovered']);
    // route for get patient by status = dead
    Route::get('/patients/status/dead', [PatientController::class, 'searchDead']);

    // API route for logout user
    Route::post('/logout', [AuthController::class, 'logout']);
});
