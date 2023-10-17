<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $products = Product::latest("id")->paginate(10)->withQueryString();

        $products = Product::when(request()->has("keyword"), function ($query) {
            $query->where(function (Builder $builder) {
                $keyword = request()->keyword;

                $builder->where("name", "like", "%" . $keyword . "%");
            });
        })->when(request()->has('id'), function ($query) {
            $sortType = request()->id ?? 'asc';
            $query->orderBy("id", $sortType);
        })->when(request()->has('name'), function ($query) {
            $sortType = request()->name ?? 'asc';
            $query->orderBy('name', $sortType);
        })->latest("id")->paginate(10)->withQueryString();

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        Gate::authorize("admin");

        $product = Product::create([
            "name" => $request->name,
            "actual_price" => $request->actual_price,
            "sale_price" => $request->sale_price,
            "total_stock" => $request->total_stock,
            "user_id" => Auth::id(),
            "unit" => $request->unit,
            "more_information" => $request->more_information,
            "brand_id" => $request->brand_id,
            "photo" => $request->photo ?? config("info.default_contact_photo"),
        ]);

        $stock = Stock::create([
            "user_id" => 1,
            "product_id" => $product->id,
            "quantity" => $request->total_stock
        ]);

        return new ProductDetailResource($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);
        if (is_null($product)) {
            return response()->json([
                // "success" => false,
                "message" => "Product not found",

            ], 404);
        }

        return new ProductDetailResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id)
    {
        Gate::authorize("admin");

        $product = Product::find($id);
        $stock = Stock::find($id);
        if (is_null($product)) {
            return response()->json([
                // "success" => false,
                "message" => "Product not found",

            ], 404);
        }

        $product->update([
            "name" => $request->name,
            "actual_price" => $request->actual_price,
            "sale_price" => $request->sale_price,
            "unit" => $request->unit,
            "more_information" => $request->more_information,
            "brand_id" => $request->brand_id,
            "total_stock" => $request->total_stock,
            // "user_id" => Auth::id(),
            "photo" => $request->photo
        ]);

        $stock->update([
            "quantity" => $product->total_stock
        ]);


        return new ProductDetailResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        $stock = Stock::find($product->id);

        if (is_null($product)) {
            return response()->json([
                // "success" => false,
                "message" => "Product not found",

            ], 404);
        }

        $product->delete();
        $stock->delete();

        return response()->json([
            "message" => "product deleted",
        ], 204);
    }
}
