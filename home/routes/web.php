<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\PostsController;
use Illuminate\Support\Facades\Route;

/** LOGIN ROUTES */
Route::post("/signUp", [LoginController::class, 'signUp']);
Route::post("/signIn", [LoginController::class, 'signIn']);
/** END LOGIN ROUTES */

/** POSTS ROUTES */
Route::post("/posts", [PostsController::class, 'getFeedPosts']);
Route::get("/post/user/{postId}", [PostsController::class, 'getUserPost']);
Route::post("/createPost", [PostsController::class, 'createPost']);
Route::post("/deletePost", [PostsController::class, 'deletePost']);
Route::post("/editPost", [PostsController::class, 'editPost']);
Route::post("/reportPost", [PostsController::class, 'reportPost']);
Route::get("/like/{postId}", [PostsController::class, 'likePost']);
Route::get("/dislike/{postId}", [PostsController::class, 'dislikePost']);
Route::get("/remove-dislike/{postId}", [PostsController::class, 'removeDislikePost']);
/** END POSTS ROUTES */

