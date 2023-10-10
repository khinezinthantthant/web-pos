<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\UpdateStockRequest;
use App\Http\Resources\StockResource;
use App\Models\Product;
use App\Models\Stock;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $stocks = Stock::latest("id")->paginate(5)->withQueryString();

        return StockResource::collection($stocks);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreStockRequest $request)
    // {
    //     Gate::authorize("admin");

    //     $productIds = Stock::where("product_id",$request->product_id)->pluck("id");
    //     // return $productIds;
    //     if(count($productIds) === 0){
    //         $stock = Stock::create([
    //             "user_id" => Auth::id(),
    //             "product_id" => $request->product_id,
    //             "quantity" => $request->quantity,
    //             "more" => $request->more
    //         ]);

    //         $totalStock = Stock::where("product_id", request()->product_id)->sum("quantity");

    //         $product = Product::find(request()->product_id);
    //         $product->total_stock  = $totalStock;
    //         $product->save();

    //         return new StockResource($stock);
    //     }

    //     $stock = Stock::find(request()->product_id);
    //     $stock->quantity += $request->quantity;
    //     $stock->save();

    //     $product = Product::find(request()->product_id);
    //     $product->total_stock = $stock->quantity;
    //     $product->save();

    //     return new StockResource($stock);
    // }
    public function store(StoreStockRequest $request)
    {
        try {
            DB::beginTransaction();

            $stock = new Stock();
            $stock->user_id = Auth::id();
            $stock->product_id = $request->product_id;
            $stock->quantity = $request->quantity;
            $stock->more = $request->more;

            $product = Product::find($request->product_id);
            $product->total_stock +=  $request->quantity;
            $product->update();
            
            $stock->save();
            DB::commit();

            return response()->json(["message" => "Stock create successful"]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
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
}
