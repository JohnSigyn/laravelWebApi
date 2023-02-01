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

// PRODUCT API
// Route::post('/products', ProductController::class);

// const config = {
//     headers: { Authorization: `Bearer ${token}` }
// };

// const bodyParameters = {
//    key: "value"
// };

// Axios.post( 
//   'http://localhost:8000/api/v1/get_token_payloads',
//   bodyParameters,
//   config
// ).then(console.log).catch(console.log);

// let data = new FormData();
// data.append('file', document.getElementById('file').files[0]);

// axios.post('/Upload/File',data).then(function (response) {
//     console.log(response.data);
// });
