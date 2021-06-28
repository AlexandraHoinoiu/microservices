<?php

use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Route;

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

Route::get("/search/{word}", [APIController::class, 'searchWord']);

Route::post("/control-panel/disableUser", [APIController::class, 'disableUser']);
Route::post("/control-panel/deleteUser", [APIController::class, 'deleteUser']);
Route::post("/control-panel/deleteReport", [APIController::class, 'deleteReport']);
Route::post("/control-panel/addSupervisor", [APIController::class, 'addSupervisor']);
Route::post("/control-panel/login", [APIController::class, 'login']);
Route::get("/control-panel/supervisors", [APIController::class, 'getSupervisors']);
Route::get("/control-panel/reports", [APIController::class, 'getReports']);
Route::get("/control-panel/posts", [APIController::class, 'getPosts']);
Route::get("/control-panel/users", [APIController::class, 'getUsers']);
