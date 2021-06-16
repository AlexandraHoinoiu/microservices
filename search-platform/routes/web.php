<?php

use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;
Route::get("/search/{word?}",[SearchController::class, "search"]);

