<?php

use App\Http\Controllers\FollowController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PostsController;
use Illuminate\Support\Facades\Route;

/** LOGIN ROUTES */
Route::post("/signUp", [LoginController::class, 'signUp']);
Route::post("/signIn", [LoginController::class, 'signIn']);
/** END LOGIN ROUTES */

/** POSTS ROUTES */
Route::post("/posts", [PostsController::class, 'getFeedPosts']);
Route::post("/createPost", [PostsController::class, 'createPost']);
Route::post("/deletePost", [PostsController::class, 'deletePost']);
Route::post("/editPost", [PostsController::class, 'editPost']);
/** END POSTS ROUTES */

/** FOLLOW ROUTES */
Route::post("/follow", [FollowController::class, 'followUser']);
Route::post("/unfollow", [FollowController::class, 'unfollowUser']);
/** END FOLLOW ROUTES */
