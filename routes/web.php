<?php

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('get/users', function () {
    return User::all();
})->name('get.users');
Route::get('get/courses', function () {
    return Course::all();
})->name('get.users');
