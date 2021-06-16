<?php

use App\Http\Controllers\FollowController;
use App\Http\Controllers\PostsProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post("/posts", [PostsProfileController::class, 'getProfilePosts']);
Route::get("/user/{type}/{userId}", [UserController::class, 'getUserDetails']);
Route::get("/user-following/{type}/{userId}", [UserController::class, 'getFollowingUsers']);
Route::post("/follow", [FollowController::class, 'followUser']);
Route::post("/unfollow", [FollowController::class, 'unfollowUser']);
Route::post("/checkUserFollow", [FollowController::class, 'checkUser']);
Route::post("/user/changePhoto", [UserController::class, 'changePhoto']);
Route::post("/user/editInfo", [UserController::class, 'editUser']);
Route::get("suggested-users/{type}/{email}",[FollowController::class, "suggestedUsers"]);

