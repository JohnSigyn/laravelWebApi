<?php

use App\Http\Controllers\UserController;

use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

// Route Guard
Route::post('/', function(){
    return response('OK', 200)
    ->header('Content-Type', 'text/plain');
});
Route::get('/', function(){
    return response('OK', 200)
    ->header('Content-Type', 'text/plain');
});


// USER API
Route::post('/register', [UserController::class,'register']);
Route::post('/login', [UserController::class,'login']);
Route::middleware('auth:sanctum')->post('/user', [UserController::class, 'getUser']);
