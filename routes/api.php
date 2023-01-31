<?php

use App\Models\User;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// // Route Guard
// Route::post('/', function(){
//     return response('OK', 200)
//     ->header('Content-Type', 'text/plain');
// });
// Route::get('/', function(){
//     return response('OK', 200)
//     ->header('Content-Type', 'text/plain');
// });
// Route::get('/test', function(){
//     return "asd";
// });

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', function (Request $request) {
    $user = User::create([
        "name" => $request->name,
        "email" => $request->email,
        "password" => Hash::make($request->password)
    ]);
    $token = $user->createToken('API TOKEN')->plainTextToken;
    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
    ]);
});
Route::post('/login', function (Request $request) {
    try {
        $request->validate([
            'email' => 'required|email|string',
            'password' => 'required|string',

        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            // NOTE:  Wrong password or user does not exist
            // 403: Forbidden
            return response()->noContent(403);
        }
        return response()->json([
            'status' => true,
            'message' => 'User Logged In Successfully',
            'token' => $user->createToken("API TOKEN")->plainTextToken
        ], 200);
    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
    }
});

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
