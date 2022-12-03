<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SendMailController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//Authentication
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

//Check Login
Route::middleware(['checklogin'])->group(function () {
    Route::get('/login', [AuthController::class, 'getLogin'])->name('login.get');
    Route::get('/login', [AuthController::class, 'getLogin'])->name('login.get');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'getRegister'])->name('register.get');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    Route::get('/checkInfo/{social}', [SocialController::class, 'checkInfo']);

    Route::get('/forgot-password', [SendMailController::class, 'getForgotPassword'])->name('get.forgotpassword');
    Route::post('/forgot-password', [SendMailController::class, 'postForgotPassword']);
    Route::get('/reset-password/{token}', [SendMailController::class, 'resetPassword']);
    Route::post('/reset-password/{token}', [SendMailController::class, 'postResetPassword']);
});

//User after Login
Route::middleware(['checkuserlogin'])->group(function () {
    Route::get('/', function () {
        return redirect(route('home'));
    });
    Route::get('/profile', [UserController::class, 'getProfile'])->name('home');
    Route::post('/profile', [UserController::class, 'saveProfile'])->name('saveProfile');
    Route::get('/change-password', [UserController::class, 'getChangePassWord'])->name('getChangePassWord');
    Route::post('/change-password', [UserController::class, 'postChangePassWord'])->name('postChangePassWord');
    Route::get('/send-code-verify-email', [SendMailController::class, 'getsendCodeVerifyEmail'])->name('sendVerifyEmail.get');
    Route::post('/send-code-verify-email', [SendMailController::class, 'postsendCodeVerifyEmail']);
    Route::get('/send-code-verify-email/{token}', [SendMailController::class, 'resetPassword']);
    Route::post('/send-code-verify-email/{token}', [SendMailController::class, 'postResetPassword']);
});
//Admin after login

Route::prefix('admin')->middleware(['auth.admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/user-management', [UserController::class, 'getAllUser'])->name('alluser');
    Route::get('/add-user', [UserController::class, 'formAddUser']);
    Route::post('/add-user', [UserController::class, 'addUser']);
    Route::get('/delete-user/{id}', [UserController::class, 'deleteUser']);
    Route::get('/block-user/{id}', [UserController::class, 'blockUser']);
    Route::get('/unblock-user/{id}', [UserController::class, 'unBlockUser']);
    Route::get('/edit-user/{id}', [UserController::class, 'editUser']);
    Route::post('/edit-user/{id}', [UserController::class, 'updateUser']);
    Route::get('/send-mail/{id}', [UserController::class, 'getSendMail']);
    Route::post('/send-mail/{id}', [UserController::class, 'postSendMail']);
    Route::get('/export-excel', [UserController::class, 'exportExcel']);
});
