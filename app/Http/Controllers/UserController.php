<?php

namespace App\Http\Controllers;

use App\Models\Store;
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

        if ($request->store_id) {
            $store = Store::where("id", $request->store_id)->first();
            $user = User::create([

                "name" => $request->name,
                "email" => $request->email,
                "phone" => $request->phone,
                "password" => Hash::make($request->password),
                "store_id" => $store->id,
            ]);
            $user->assignRole('worker');
        } else {
            $validated = $request->validate([
                "store_name" => 'required|unique:App\Models\Store,name',
                "store_address" => 'required',
                "store_invoice" => 'required',
                "store_gst" => 'required',
                "store_pan" => 'required',
                "store_loyalty_value" => 'required',
                "store_loyalty_given" => 'required',
                "store_gst" => 'required',
                "store_low_stock_threshhold" => 'required',
                "store_license" => 'required',
            ]);
            $store = Store::create([
                "name" => $request->store_name,
                "proprietor" => $request->name,
                "address" => $request->store_address,
                "phone" => $request->phone,
                "invoice" => $request->store_invoice,
                "gst" => $request->store_gst,
                "pan" => $request->store_pan,
                "loyalty_value" => $request->store_loyalty_value,
                "loyalty_given" => $request->store_loyalty_given,
                "gst_applicable" => $request->store_gst_applicable,
                "low_stock" => $request->store_low_stock_threshhold,
                "license_type" => $request->store_license,
            ]);
            $user = User::create([

                "name" => $request->name,
                "email" => $request->email,
                "phone" => $request->phone,
                "password" => Hash::make($request->password),
                "store_id" => $store->id,
            ]);
            $user->assignRole('admin');
        }


        $token = $user->createToken('API TOKEN')->plainTextToken;
        return response()->json([
            'user' => $user,
            'store' => $store,
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
            $user->tokens()->delete();

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

        $user = User::where("id", $request->user()->id)->first();
        $user->getRoleNames();
        $store = Store::findOrFail($user->id);
        $user["store"] = $store;
        return response()->json($user);
    }
}
