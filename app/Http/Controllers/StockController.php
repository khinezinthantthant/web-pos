<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\UpdateStockRequest;
use App\Http\Resources\StockResource;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

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
    public function store(StoreStockRequest $request)
    {
        $stock = Stock::create([
            "user_id" => Auth::id(),
            "product_id" => $request->product_id,
            "quantity" => $request->quantity,
            "more" => $request->more
        ]);

        $this->syncProductTotalStock();

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
    public function update(UpdateStockRequest $request, string $id)
    {
        $stock = Stock::find($id);
        if (is_null($stock)) {
            return response()->json([
                // "success" => false,
                "message" => "Stock not found",

            ], 404);
        }

        $stock->update([
            "user_id" => Auth::id(),
            "product_id" => $request->product_id,
            "quantity" => $request->quantity,
            "more" => $request->more
        ]);

        $this->syncProductTotalStock();

        return new StockResource($stock);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $stock = Stock::find($id);
        if (is_null($stock)) {
            return response()->json([
                // "success" => false,
                "message" => "Stock not found",

            ], 404);
        }

        $stock->delete();
        $this->syncProductTotalStock();

        return response()->json([
            "message" => "stock deleted"
        ], 204);
    }


    private function syncProductTotalStock() :void
    {
        $totalStock = Stock::where("product_id", request()->product_id)->sum("quantity");

        $product = Product::find(request()->product_id);
        $product->total_stock = $totalStock;
        $product->save();
    }
}
