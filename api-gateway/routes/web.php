<?php

use App\Http\Controllers\APIController;
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
    return view('welcome');
});
Route::post("/home/signIn", [APIController::class, 'signIn']);
Route::post("/home/signUp", [APIController::class, 'signUp']);
Route::post("/home/posts", [APIController::class, 'getFeedPosts']);
Route::post("/home/createPost", [APIController::class, 'createPosts']);
Route::post("/home/deletePost", [APIController::class, 'deletePost']);
Route::post("/home/editPost", [APIController::class, 'editPost']);
Route::post("/home/reportPost", [APIController::class, 'reportPost']);
Route::get("/home/post/user/{postId}", [APIController::class, 'getUserPosts']);
Route::get("/home/like/{postId}", [APIController::class, 'likePost']);


Route::post("/profile/follow", [APIController::class, 'followUser']);
Route::post("/profile/unfollow", [APIController::class, 'unfollowUser']);
Route::post("/profile/posts", [APIController::class, 'getProfilePosts']);
Route::post("/profile/checkUserFollow", [APIController::class, 'checkUserFollow']);
Route::post("/profile/user/changePhoto", [APIController::class, 'changePhoto']);
Route::post("/profile/user/editInfo", [APIController::class, 'editInfo']);
Route::get("/profile/suggested-users/{type}/{email}/{limit?}", [APIController::class, 'suggestedUsers']);
Route::get("/profile/user-followers/{type}/{userId}/{limit?}", [APIController::class, 'getFollowersUsers']);
Route::get("/profile/user-following/{type}/{userId}/{limit?}", [APIController::class, 'getFollowingUsers']);
Route::get("/profile/user/{type}/{userId}", [APIController::class, 'getUserDetails']);
