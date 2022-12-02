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
Route::get('/', function () {
    return redirect(route('home'));
});
Route::get('/profile', function () {
    return view('welcome');
})->name('home');

//Authentication
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');
Route::get('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout.get');

//User
Route::middleware(['checklogin'])->group(function () {
    Route::get('/login', [AuthController::class, 'getLogin'])->name('login.get');
    Route::get('/login', [AuthController::class, 'getLogin'])->name('login.get');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'getRegister'])->name('register.get');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    Route::get('/checkInfo/{social}', [SocialController::class, 'checkInfo']);
    Route::get('/send-code-verify-email', [SendMailController::class, 'sendCodeVerifyEmail']);
});

//Admin
Route::prefix('admin')->middleware(['checkadminlogin'])->group(function () {
    Route::get('/login', [AuthController::class, 'getLogin'])->name('admin.login.get');
});

Route::prefix('admin')->middleware(['auth.admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('welcome');
    })->name('dashboard');
    Route::get('/user-management', [UserController::class, 'getAllUser'])->name('alluser');
    Route::get('/add-user', [UserController::class, 'formAddUser']);
    Route::post('/add-user', [UserController::class, 'addUser']);
    Route::get('/delete-user/{id}', [UserController::class, 'deleteUser']);
    Route::get('/block-user/{id}', [UserController::class, 'blockUser']);
    Route::get('/unblock-user/{id}', [UserController::class, 'unBlockUser']);
    Route::get('/edit-user/{id}', [UserController::class, 'editUser']);
    Route::post('/edit-user/{id}', [UserController::class, 'updateUser']);
});
