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
            $vouchers = Auth::user()->vouchers()->whereDate('created_at', Carbon::today())->get();
        }
        // return VoucherResource::collection($vouchers);
        return new DailySaleOverviewResource($vouchers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $productIds = collect($request->items)->pluck("product_id");

        try {
            DB::beginTransaction();

            $products = Product::whereIn("id", $productIds)->get();

            $total = 0;

            foreach ($request->items as $item) {
                $total += $item["quantity"] * Product::find($item["product_id"])->sale_price;
            }


            $tax = $total * 0.05;
            $netTotal = $total + $tax;

            $voucher = new Voucher();
            $voucher->customer_name = $request->customer_name;
            $voucher->phone_number = $request->phone_number;
            $voucher->voucher_number =  rand(1, 100);
            $voucher->total = $total;
            $voucher->tax = round($tax,2);
            $voucher->net_total = $netTotal;
            $voucher->user_id = Auth::id();

            $voucher->save();

            // return $voucher;
            $records = [];

            foreach ($request->items as $item) {

                $currentProduct = $products->find($item["product_id"]);
                // return $currentProduct;
                if ($currentProduct->total_stock >= $item["quantity"]) {
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

                    // Stock::where("id", $item["product_id"])->update([
                    //     "quantity" => $currentProduct->total_stock - $item["quantity"]
                    // ]);
                }
            }

            $voucherRecords = VoucherRecord::insert($records); //use database
            // return $voucherRecords;
            DB::commit();
            return response()->json([
                'message' => 'checkout successful',
                "data" => new VoucherDetailResource($voucher)
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }



        // dd($voucherRecords);
        return new VoucherDetailResource($voucher);
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

    public function overview()
    {
        $totalStock = Stock::all()->sum("quantity");
        $totalStaff = User::all()->count();
        // return $totalStaff;

        $yearlySales = Voucher::selectRaw('YEAR(created_at) as year,SUM(total) as total')
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->get();


        $monthlySales = Voucher::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(total) as total')
        )
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();


        $formatedMonthlySales = $monthlySales->map(function ($item) {
            $dateObj = DateTime::createFromFormat('!m', $item->month);
            $monthName = $dateObj->format("F");

            return [
                'month' => $monthName,
                'year' => $item->year,
                'total' => $item->total
            ];
        });


        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();
        $sales = Voucher::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("DATE(created_at) as date, SUM(total) as total")
            ->groupBy("date")
            ->orderBy('date')
            ->get();

        $count = $sales->pluck("date")->count();
        $date = [];
        $dayName = [];
        $weekelySales = [];
        for ($i = 0; $i < $count; $i++) {
            $date[] = Carbon::parse($sales->pluck("date")[$i]);
            $dayName[] = $date[$i]->format("l");

            $weekelySales[] =  [
                "total" => $sales->pluck("total")[$i],
                "dayName" => $dayName[$i]
            ];
        }

        return response()->json([
            "total_stocks" => $totalStock,
            "total_staff" => $totalStaff,
            "yearly_sales" => $yearlySales,
            "monthly_sales" => $formatedMonthlySales,
            "weekely_sales" => $weekelySales,
        ]);
    }
}
