<?php

use App\Http\Controllers\Api\CatalogController;
use Illuminate\Support\Facades\Route;

Route::get('categories', [CatalogController::class, 'categories']);
Route::get('courses', [CatalogController::class, 'courses']);
Route::get('courses/{id}', [CatalogController::class, 'course']);
Route::get('teachers', [CatalogController::class, 'teachers']);
Route::get('events', [CatalogController::class, 'events']);
