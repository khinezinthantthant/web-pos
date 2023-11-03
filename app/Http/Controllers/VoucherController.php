<?php

namespace App\Http\Controllers;

use App\Http\Resources\DailySaleOverview;
use App\Http\Resources\DailySaleOverviewResource;
use App\Http\Resources\VoucherDetailResource;
use App\Http\Resources\VoucherResource;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherRecord;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;
use Termwind\Components\Raw;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if (Auth::user()->role == "admin") {
            $vouchers = Voucher::whereDate('created_at', Carbon::today())->paginate(10)->withQueryString();
        } else {
            // $vouchers = Auth::user()->vouchers()->whereDate('created_at', Carbon::today())->get();

            $vouchers = Auth::user()->vouchers()->whereDate('created_at', Carbon::today())->paginate(10)->withQueryString();
        }
        // return $vouchers;

        // return VoucherResource::collection($vouchers);
        return new DailySaleOverviewResource($vouchers);
    }

    /**
     * Store a newly created resource in storage.
     */

    //  old code
    // public function store(Request $request)
    // {
    //     $productIds = collect($request->items)->pluck("product_id");

    //     try {
    //         DB::beginTransaction();

    //         $products = Product::whereIn("id", $productIds)->get();
    //         $total = 0;

    //         foreach ($request->items as $item) {
    //             $total += $item["quantity"] * Product::find($item["product_id"])->sale_price;
    //         }

    //         $tax = $total * 0.05;
    //         $netTotal = $total + $tax;

    //         $id = Voucher::all()->last()->id;

    //         $voucher = new Voucher();
    //         $voucher->customer_name = $request->customer_name;
    //         $voucher->phone_number = $request->phone_number;
    //         $voucher->voucher_number = $id + 1;
    //         $voucher->total = $total;
    //         $voucher->tax = $tax;
    //         $voucher->net_total = $netTotal;
    //         $voucher->user_id = Auth::id();

    //         $voucher->save();

    //         $records = [];
    //         $product = [];

    //         foreach ($request->items as $item) {

    //             $currentProduct = $products->find($item["product_id"]);

    //             $product[] = [
    //                 "product_id" => $currentProduct->id,
    //                 "quantity" => $currentProduct->total_stock
    //             ];

    //             $records[] = [
    //                 "voucher_id" => $voucher->id,
    //                 "product_id" => $item["product_id"],
    //                 "price" => $products->find($item["product_id"])->sale_price,
    //                 "quantity" => $item["quantity"],
    //                 "actual_price" => 0,
    //                 "cost" => $item["quantity"] * $currentProduct->sale_price,
    //                 "created_at" => now(),
    //                 "updated_at" => now()
    //             ];


    //             Product::where("id", $item["product_id"])->update([
    //                 "total_stock" => $currentProduct->total_stock - $item["quantity"]
    //             ]);

    //             Stock::where("id", $item["product_id"])->update([
    //                 "quantity" => $currentProduct->total_stock - $item["quantity"]
    //             ]);
    //         }

    //         $voucherRecords = VoucherRecord::insert($records); //use database
    //         // dd($voucherRecords);

    //         DB::commit();
    //         return new VoucherDetailResource($voucher);
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return response()->json(['message' => $e->getMessage()], 500);
    //     }
    // }


    public function store(Request $request){
        try {
            DB::beginTransaction();

            $productIds = collect($request->items)->pluck("product_id");
            $products = Product::whereIn("id", $productIds)->get(); // use database
            $totalActualPrice = 0;
            $total = 0;

            foreach ($request->items as $item) {
                $currentProduct = $products->find($item["product_id"]);

                $totalActualPrice += $item["quantity"] * $currentProduct->actual_price;
                $total += $item["quantity"] * $currentProduct->sale_price;
            }
            $tax = $total * 0.05;
            $netTotal = $total + $tax;

            // $id = Voucher::all()->last()->id;

            $voucherNumber = rand(1000,9999);
            $voucher = new Voucher();
            $voucher->customer_name = $request->customer_name;
            $voucher->phone_number = $request->phone_number;
            $voucher->voucher_number = $voucherNumber;
            $voucher->total_actual_price = $totalActualPrice;
            $voucher->total = $total;
            $voucher->tax = $tax;
            $voucher->net_total = $netTotal;
            $voucher->user_id = Auth::id();

            $voucher->save();

            $records = [];

            foreach ($request->items as $item) {

                $currentProduct = $products->find($item["product_id"]);
                $records[] = [
                    "voucher_id" => $voucher->id,
                    "product_id" => $item["product_id"],
                    "actual_price" => $currentProduct->actual_price,
                    "price" => $currentProduct->sale_price,
                    "quantity" => $item["quantity"],
                    "cost" => $item["quantity"] * $currentProduct->sale_price,
                    "created_at" => now(),
                    "updated_at" => now()
                ];
                Product::where("id", $item["product_id"])->update([
                    "total_stock" => $currentProduct->total_stock - $item["quantity"]
                ]);
                Stock::where("id", $item["product_id"])->update([
                    "quantity" => $currentProduct->total_stock - $item["quantity"]
                ]);
            }

            $voucherRecords = VoucherRecord::insert($records); // use database
            // dd($voucherRecords);
            // return $request;

            DB::commit();
            return response()->json([
                "message" => "checkout successful",
                "data" => new VoucherDetailResource($voucher)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(["message" => $e->getMessage()], 500);
        }

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
        return abort(403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return abort(403);
    }
}
