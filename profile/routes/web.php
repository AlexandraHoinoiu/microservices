<?php

use App\Http\Controllers\PostsProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post("/posts", [PostsProfileController::class, 'getProfilePost']);
Route::get("/user/{type}/{userId}", [UserController::class, 'getUserDetails']);
Route::get("/user-following/{type}/{userId}", [UserController::class, 'getFollowingUsers']);

