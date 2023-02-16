<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleHistory;
use App\Models\SaleProduct;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule as ValidationRule;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation
        $validate = $request->validate([
            "invoice" => "required|boolean",
            "paid_amount" => "required|numeric|min:0",
            "customer_id" => "exists:customers,id",
            "sale_mode" => [ValidationRule::in(['online', 'walk_in'])],
            "payment_mode" => [ValidationRule::in(["cash", "upi", "gpay", "cheque", "card"])],
            "products" => "array|min:1|required",
            "products.*.id" => "numeric|required",
            "products.*.quantity" => "numeric|required",
            "products.*.discount" => "numeric|required",
        ]);
        $sale_temp = [];
        $store_id = $request->user()["store_id"];

        // Check if invoice should be genreated
        if ($request->invoice) {
            if (Carbon::now()->month > 3) {
                $financial_year = Carbon::now()->year;
            } else {
                $financial_year = Carbon::now()->year - 1;
            }
            $financial_start = Carbon::create($financial_year, 4, 1, 0, 0, 0, 'UTC');
            $invoice_count = Sale::where("store_id", $store_id)
                ->whereDate("created_at", ">=", $financial_start)->whereDate("created_at", "<=", $financial_start->addYear()->subSecond())->count();
            $sale_temp["invoice_no"] = $invoice_count;
        } else {
            $sale_temp["invoice_no"] = null;
        }
        $sale_temp["customer_id"] = $request->customer_id;
        $sale_temp["store_id"] = $store_id;
        $sale_temp["loyalty_point"] = $request->loyalty_point ? $request->loyalty_point : 0;
        $sale_temp["sale_mode"] = $request->sale_mode;

        $sale = Sale::create($sale_temp);

        foreach ($request->products as $product) {
            $product_init = Product::where("store_id", $store_id)->where("id", $product["id"])->first();
            $product_init = new ProductResource($product_init);
            SaleProduct::create([
                "store_id" => $product_init["store_id"],
                "sale_id" => $sale->id,
                "product_id" => $product_init["id"],
                "sellingPrice" => $product_init["sellingPrice"],
                "tax" => $product_init["tax"],
                "costPrice" => $product_init["costPrice"],
                "discount" => ($product_init["discount"] * $product["quantity"]) + $product["discount"],
                "quantity" => $product["quantity"],
            ]);
            $product_init->decrement("quantity", $product["quantity"]);
        }

        SaleHistory::create([
            "sale_id" => $sale->id,
            "store_id" => $store_id,
            "paid_amount" => $request->paid_amount,
            "payment_mode" => $request->payment_mode,
        ]);



        return $sale;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function show(Sale $sale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function edit(Sale $sale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sale $sale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sale $sale)
    {
        //
    }
}
