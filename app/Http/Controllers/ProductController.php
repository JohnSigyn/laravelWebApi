<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Product::where("store_id", "=", $request->user()["store_id"])->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $store_id = strval($request->user()["store_id"]);

        if ($request->input("store_id") != $store_id) {
            return response()->json([
                "message" => "You are trying to access others store",
                "errors" => ([
                    "auth" => "Users Accessing other stores"
                ])
            ]);
        }
        $sellingPrice = (1 + ($request->tax) / 100) * $request->sellingPrice;
        if ($request->discountInPercent == "1") {
            $discountMax = 100;
        } else {
            $discountMax =  $sellingPrice;
        }
        $costPrice = $request->costPrice;

        $validate = $request->validate([
            "name" => "required",
            "store_id" => "required|numeric|exists:stores,id",
            "sellingPrice" => "required|numeric|min:$costPrice",
            "tax" => "required|numeric|max:100",
            "costPrice" => "required|numeric",
            "discount" => "required|numeric|max:$discountMax",
            "quantity" => "required|numeric",
            "discountInPercent" => "required",
        ]);
        $product = Product::create($request->all());
        return $product;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product, Request $request)
    {

        if ($request->user()["store_id"] == $product->store_id)  return $product;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        // validation
        $store_id = strval($request->user()["store_id"]);

        if ($request->input("store_id") != $store_id) {
            return response()->json([
                "message" => "You are trying to access others store",
                "errors" => ([
                    "auth" => "Users Accessing other stores"
                ])
            ]);
        }
        $sellingPrice = (1 + ($request->tax) / 100) * $request->sellingPrice;
        if ($request->discountInPercent == "1") {
            $discountMax = 100;
        } else {
            $discountMax =  $sellingPrice;
        }
        $costPrice = $request->costPrice;

        $validate = $request->validate([
            "name" => "required",
            "store_id" => "required|numeric|exists:stores,id",
            "sellingPrice" => "required|numeric|min:$costPrice",
            "tax" => "required|numeric|max:100",
            "costPrice" => "required|numeric",
            "discount" => "required|numeric|max:$discountMax",
            "quantity" => "required|numeric",
            "discountInPercent" => "required",
        ]);
        return    $product->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product,Request $request)
    {
        if ($request->user()["store_id"] == $product->store_id){
            $product = $product->delete();
            return $product;
        }
        else{
            return response()->json([
                "auth"=>"Access denied",
            ]);
        }
    }
}
