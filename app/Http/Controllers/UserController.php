<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{


    public function register(Request $request)
    {

        $validated = $request->validate([
         
            "name" => 'required|max:255',
            "email" => 'required|unique:users|max:255',
            "phone" => 'required|unique:users|max:255',
            "password" => 'required|min:8|max:255',
        ]);
  
        $user = User::create([

            "name" => $request->name,
            "email" => $request->email,
            "phone" => $request->phone,
            "password" => Hash::make($request->password)
        ]);
        $token = $user->createToken('API TOKEN')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
    public function login(Request $request)
    {
        try {
            // $request->validate([
            //     'email' => 'required|email|string',
            //     'password' => 'required|string',

            // ]);
            $user = User::where('email', $request->email)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
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
    }
    public function getUser(Request $request)
    {

        return $request->user();
    }
}
