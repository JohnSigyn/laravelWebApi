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
    
    public function index(Request $request)
    {
        return Sale::where('store_id', $request->user()->store_id)->with('product', 'history')->get();
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


        $this->createSale($sale_temp, $request->products, $store_id, $request->paid_amount, $request->payment_mode);
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
    public function edit(Sale $sale, Request $request)
    {
        $validate = $request->validate([
            "paid_amount" => "required|numeric|min:0",
            "customer_id" => "exists:customers,id",
            "sale_mode" => [ValidationRule::in(['online', 'walk_in'])],
            "payment_mode" => [ValidationRule::in(["cash", "upi", "gpay", "cheque", "card"])],
            "products" => "array|min:1|required",
            "products.*.id" => "numeric|required",
            "products.*.quantity" => "numeric|required",
            "products.*.discount" => "numeric|required",
        ]);


        $saleProduct = SaleProduct::where('sale_id', $sale->id)->get();
        foreach ($saleProduct as $product) {
            Product::where('id', $product['product_id'])->increment('quantity', $product['quantity']);
        }
        SaleProduct::where('sale_id', $sale->id)->delete();
        SaleHistory::where('sale_id', $sale->id)->delete();
        $store_id =  $request->user()['store_id'];

        $saleData = [];
        $saleData['invoice_no'] = $sale->invoice_no;
        $saleData["customer_id"] = $request->customer_id;
        $saleData["store_id"] = $store_id;
        $saleData["loyalty_point"] = $request->loyalty_point ? $request->loyalty_point : 0;
        $saleData["sale_mode"] = $request->sale_mode;

        $this->createSale($saleData, $request->products, $store_id, $request->paid_amount, $request->payment_mode);
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
        $totalPaid = SaleHistory::where('sale_id', $sale->id)->sum('paid_amount');
        $saleProduct = SaleProduct::where('sale_id', $sale->id)->get();
        $totalPayable = 0;
        foreach ($saleProduct as $product) {
            $totalPayable = ($totalPayable + $product['sellingPrice'] * (1 + $product['tax'] / 100)) * $product['quantity'];
        }

        $totalPayable =   $totalPayable - $totalPaid;
        $request->validate([
            "paid_amount" => "required|numeric|max:$totalPayable|min:1",
            "payment_mode" => ['required', ValidationRule::in(["cash", "upi", "gpay", "cheque", "card"])],
        ]);
        SaleHistory::create([
            "sale_id" => $sale->id,
            "store_id" => $request->user()['store_id'],
            "paid_amount" => $request->paid_amount,
            "payment_mode" => $request->payment_mode,
        ]);
        return $totalPayable - $request->paid_amount;
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

    public function createSale($saleData, $productData, $storeId, $paidAmount, $paymentMode)
    {
        $sale = Sale::create($saleData);
        foreach ($productData as $product) {
            $product_init = Product::where("store_id", $storeId)->where("id", $product["id"])->first();
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
            "store_id" => $storeId,
            "paid_amount" => $paidAmount,
            "payment_mode" => $paymentMode,
        ]);



        return $sale;
    }
}
