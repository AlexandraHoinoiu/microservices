<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::get("/posts", [DashboardController::class, 'getPosts']);
Route::get("/users", [DashboardController::class, 'getUsers']);
Route::get("/reports", [DashboardController::class, 'getReports']);
Route::get("/supervisors", [DashboardController::class, 'getSupervisors']);
Route::post("/login", [LoginController::class, 'login']);
Route::post("/addSupervisor", [LoginController::class, 'addSupervisor']);
Route::post("/deleteReport", [DashboardController::class, 'deleteReport']);
