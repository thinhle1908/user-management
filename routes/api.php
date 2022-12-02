<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SendMailController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\UserController;
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

// Route::middleware(['auth:sanctum'])->group(function () {
//     Route::get('/send-code-verify-email', [SendMailController::class, 'sendCodeVerifyEmail']);
//     Route::post('/verify-email', [SendMailController::class, 'verifyEmail']);
//     Route::get('/logout', [AuthController::class, 'logout']);
//     Route::get('/user', [AuthController::class, 'user']);


//     Route::prefix('admin')->group(function () {
//         //Admin
//         Route::post('/send-mail', [SendMailController::class, 'sendMail']);
//         Route::post('/user-management', [UserController::class, 'addUser']);
//         Route::put('/user-management/{id}', [UserController::class, 'editeUser']);
//         Route::delete('/user-management/{id}', [UserController::class, 'deleteUser']);
//         Route::get('/user-management', [UserController::class, 'getAllUser']);
//         Route::get('/search-user', [UserController::class, 'searchUser']);
//         Route::get('/filter-user', [UserController::class, 'filterUsers']);
//         Route::put('/block-user/{id}', [UserController::class, 'blockUser']);
//     });
// });

Route::group(['middleware' => ['web']], function () {
    Route::get('/getInfo/{social}', [SocialController::class, 'getInfo']);
    // Route::get('/checkInfo/{social}', [SocialController::class, 'checkInfo']);
});

// Route::post('/login', [AuthController::class, 'login'])->name('login');
// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/send-token-reset-password', [SendMailController::class, 'sendTokenResetPassword']);
// Route::post('/reset-password', [SendMailController::class, 'resetPassword']);
