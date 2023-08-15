<?php

namespace App\Http\Controllers;

use App\Http\Resources\VoucherDetailResource;
use App\Http\Resources\VoucherResource;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\VoucherRecord;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vouchers = Voucher::all();
        return VoucherResource::collection($vouchers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $productIds = collect($request->items)->pluck("product_id");
        $products = Product::whereIn("id", $productIds)->get();
        $total = 0;

        foreach ($request->items as $item) {
            $total += $item["quantity"] * Product::find($item["product_id"])->sale_price;
        }

        $tax = $total * 0.05;
        $netTotal = $total + $tax;

        $voucher = Voucher::create([
            "voucher_number" => rand(1,100),
            "total" => $total,
            "tax" => $tax,
            "net_total" => $netTotal,
            "user_id" => Auth::id()
        ]); //use database

        $records = [];

        foreach ($request->items as $item) {

            $currentProduct = $products->find($item["product_id"]);

            if($currentProduct->total_stock >= $item["quantity"]){
                $records[] = [
                    "voucher_id" => $voucher->id,
                    "product_id" => $item["product_id"],
                    "price" => $products->find($item["product_id"])->sale_price,
                    "quantity" => $item["quantity"],
                    "cost" => $item["quantity"] * $currentProduct->sale_price,
                    "created_at" => now(),
                    "updated_at" => now()
                ];

                Product::where("id", $item["product_id"])->update([
                    "total_stock" => $currentProduct->total_stock - $item["quantity"]
                ]);
            }

        }

        $voucherRecords = VoucherRecord::insert($records); //use database
        // dd($voucherRecords);
        // return $request;
        return VoucherDetailResource::collection($voucher);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $voucher = Voucher::find($id);
        if (is_null($voucher)) {
            return response()->json([
                // "success" => false,
                "message" => "Product not found",

            ], 404);
        }

        return new VoucherDetailResource($voucher);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
