<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\UpdateStockRequest;
use App\Http\Resources\StockDetailResource;
use App\Http\Resources\StockResource;
use App\Models\DailySaleOverview;
use App\Models\MonthlySaleOverview;
use App\Models\Product;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $stocks = Stock::latest("id")->paginate(5)->withQueryString();

        // return StockResource::collection($stocks);


        $products = Product::when(request()->has("keyword"), function ($query) {
            $query->where(function (Builder $builder) {
                $keyword = request()->keyword;

                $builder->where("name", "like", "%" . $keyword . "%");
            });
        })->latest("id")->paginate(5)->withQueryString();
// return $products;
        return StockDetailResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStockRequest $request)
    {
        Gate::authorize("admin");

        $productIds = Stock::where("product_id",$request->product_id)->pluck("id");
        // return $productIds;
        if(count($productIds) === 0){
            $stock = Stock::create([
                "user_id" => Auth::id(),
                "product_id" => $request->product_id,
                "quantity" => $request->quantity,
                "more" => $request->more
            ]);

            $totalStock = Stock::where("product_id", request()->product_id)->sum("quantity");

            $product = Product::find(request()->product_id);
            $product->total_stock  = $totalStock;
            $product->save();

            return new StockResource($stock);
        }

        $stock = Stock::find(request()->product_id);
        $stock->quantity += $request->quantity;
        $stock->save();

        $product = Product::find(request()->product_id);
        $product->total_stock = $stock->quantity;
        $product->save();

        return new StockResource($stock);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $stock = Stock::find($id);
        if (is_null($stock)) {
            return response()->json([
                // "success" => false,
                "message" => "Stock not found",

            ], 404);
        }

        return new StockResource($stock);
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(UpdateStockRequest $request, string $id)
    // {
    //     Gate::authorize("admin");
    //     $stock = Stock::find($id);
    //     if (is_null($stock)) {
    //         return response()->json([
    //             // "success" => false,
    //             "message" => "Stock not found",

    //         ], 404);
    //     }

    //     $stock->update([
    //         "user_id" => Auth::id(),
    //         "product_id" => $request->product_id,
    //         "quantity" => $request->quantity,
    //         "more" => $request->more
    //     ]);

    //     $this->syncProductTotalStock();

    //     return new StockResource($stock);
    // }

    /**
     * Remove the specified resource from storage.
     */


    public function destroy(string $id)
    {
        Gate::authorize("admin");
        $stock = Stock::find($id);
        if (is_null($stock)) {
            return response()->json([
                // "success" => false,
                "message" => "Stock not found",
            ], 404);
        }

        $stock->delete();

        $product = Product::find($stock->product_id);
        $totalStock = Stock::where("product_id", $product->id)->sum("quantity");
        $product->total_stock = $totalStock;
        $product->save();

        return response()->json([
            "message" => "stock deleted"
        ], 204);
    }


    public function test()
    {
        $startOfMonth = Carbon::create(2022, 7, 1);
        $sales = [];



        while($startOfMonth->format("M Y") != Carbon::now()->format("M Y")) {
            $endOfMonth = $startOfMonth->copy()->endOfMonth();
            $dailyVoucher = DailySaleOverview::whereBetween('created_at', [$startOfMonth, $endOfMonth])->get();
            $totalVoucher = $dailyVoucher->sum('total_vouchers');

            // $totalActualPrice = $dailyVoucher->sum('total_actual_price');
            $cashTotal = $dailyVoucher->sum('total_cash');
            $taxTotal = $dailyVoucher->sum('total_tax');
            $total = $dailyVoucher->sum('total');
            $sales[] = [
                "total_vouchers" => $totalVoucher,
                // "total_actual_price" => $totalActualPrice,
                "total_cash"  => $cashTotal,
                "total_tax" => $taxTotal,
                "total" => $total,
                "created_at" => $endOfMonth,
                "updated_at" => $endOfMonth,
                "month" => $endOfMonth->format('m'),
                "year" => $endOfMonth->format('Y'),
            ];
            $startOfMonth->addMonth();
        }
        MonthlySaleOverview::insert($sales);
    }


}
