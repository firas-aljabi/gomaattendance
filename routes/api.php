<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JustifyRquestController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\VacationRquestController;
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


Route::group(['middleware' => 'api'], function () {
    // Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
});


Route::group(['middleware' => 'auth:api'], function () {

    // -- Admin --//
    Route::post('create_employee', [AdminController::class, 'store']);
    Route::get('get_dashboard_data', [AdminController::class, 'getDashboardData']);
    Route::get('get_employees_list', [AdminController::class, 'getEmployeesList']);
    Route::get('get_employee/{id}', [AdminController::class, 'getEmployee']);
    Route::get('profile', [AdminController::class, 'profile']);

    // -- Posts -- //
    Route::post('create_post', [PostController::class, 'store']);
    Route::get('get_posts_list', [PostController::class, 'getPostsList']);
    Route::get('get_my_posts', [PostController::class, 'getMyPosts']);
    Route::post('add_comment', [PostController::class, 'addComment']);
    Route::post('add_like', [PostController::class, 'addLike']);
    Route::post('share_post', [PostController::class, 'sharePost']);


    // -- Vcation Requests -- //
    Route::post('add_vacation_request', [VacationRquestController::class, 'store']);
    Route::put('approve_vacation_request/{id}', [VacationRquestController::class, 'approve_vacation_request']);
    Route::put('reject_vacation_request/{id}', [VacationRquestController::class, 'reject_vacation_request']);
    Route::get('vacation_request/{id}', [VacationRquestController::class, 'show']);
    Route::get('my_vacation_requests', [VacationRquestController::class, 'getMyVacationRequests']);
    Route::get('vacation_requests', [VacationRquestController::class, 'getVacationRequests']);
    Route::get('my_monthly_shift', [VacationRquestController::class, 'getMyMonthlyShift']);


    // -- Justify Requests -- //
    Route::post('add_justify_request', [JustifyRquestController::class, 'store']);
    Route::get('justify_request/{id}', [JustifyRquestController::class, 'show']);
    Route::get('my_justify_requests', [JustifyRquestController::class, 'getMyJustifyRequests']);
    Route::get('justify_requests', [JustifyRquestController::class, 'getJustifyRequests']);
});