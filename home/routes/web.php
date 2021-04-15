<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
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

Route::post('/checkUser', [HomeController::class, 'checkUser']);
Route::post('/storeUser', [HomeController::class, 'storeUser']);
Route::post("/signUp", [LoginController::class, 'signUp']);
Route::post("/signIn", [LoginController::class, 'signIn']);
Route::get("user/{type}/{email}", [LoginController::class, 'userDetail']);
