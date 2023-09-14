<?php

namespace App\Http\Controllers;

use App\Http\Resources\StockReportResource;
use App\Http\Resources\StockResource;
use App\Models\Brand;
use App\Models\DailySaleOverview;
use App\Models\Product;
use App\Models\Stock;
use App\Models\VoucherRecord;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function stockReport(Request $request)
    {
        $brand = Product::join('brands', 'brands.id', '=', 'products.brand_id')
                ->get(['brands.name']);

        $products = Product::when(request()->has("keyword"), function ($query) {
            $query->where(function (Builder $builder) {

                $keyword = request()->keyword;
                $builder->where("name", "LIKE", "%" . $keyword . "%");
                // $builder->orWhere($brand, "LIKE", "%" . $keyword . "%");
            });
        })
        ->when($request->stock_level == 'out of stock', function (Builder $query) {

            $query->where('total_stock', "=", 0);
        })
            ->when($request->stock_level == 'low stock', function (Builder $query) {

                $query->whereBetween('total_stock', [1, 9]);
            })
            ->when($request->stock_level == 'instock', function (Builder $query) {

                $query->where('total_stock', ">=", 10);
            })
            ->latest("id")
            ->paginate(10)
            ->withQueryString();

        return StockReportResource::collection($products);
    }

    public function brandReport()
    {
        $totalProducts = Product::all()->count("id");
        $totalBrands = Brand::all()->count("id");
        $outOfStock = Product::where("total_stock", "=", 0)->count("id");
        $lowStock = Product::whereBetween("total_stock", [1, 10])->count("id");
        $inStock = Product::where("total_stock",">",10)->count("id");

        return response()->json([
            "totalProducts" => $totalProducts,
            "totalBrands" => $totalBrands,
            "stocks" => [
                "inStock" => ($inStock / $totalProducts) * 100 ."%",
                "lowStock" => ($lowStock / $totalProducts) * 100 ."%",
                "outOfStock" => ($outOfStock / $totalProducts) * 100 ."%",
            ]
        ]);
    }

    public function weekelyBestSellerBrands()
    {
        // $startDate = Carbon::now()->startOfWeek();
        // $endDate = Carbon::now()->endOfWeek();

        // $products = Product::select('products.*', DB::raw('SUM(stocks.quantity) as total_entry_stock'))
        //     ->leftJoin('stocks', 'products.id', '=', 'stocks.product_id')
        //     ->whereBetween('stocks.created_at', [$startDate, $endDate])
        //     ->groupBy('products.id')
        //     ->take(5)
        //     ->get();

        // $brands = [];

        // foreach ($products as $topProduct) {
        //     $brands = $topProduct->brand->name;
        // }
        // return $brands;

        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();

        $brands = Brand::all()->pluck('name', 'id')->toArray();
        $totalBrand = [];
        foreach($brands as $brandId => $brandName){
            $saleBrand = VoucherRecord::whereBetween("created_at",[$startDate,$endDate])
            ->whereHas("product",function($query) use ($brandId){
                $query->where("brand_id",$brandId);
            })
            ->get();

            $totalBrand[] = [
                "brand_name" => $brandName,
                "total_brand_sale" => $saleBrand->sum("quantity"),
                "total_sale" => $saleBrand->sum("cost")
            ];
        }
        return $totalBrand;
    }

    public function stockOverview(Request $request)
    {
        $products = Product::when($request->stock_level == 'out of stock', function (Builder $query) {

            $query->where('total_stock', "=", 0);
        })
            ->when($request->stock_level == 'low stock', function (Builder $query) {

                $query->whereBetween('total_stock', [1, 10]);
            })
            ->when($request->stock_level == 'instock', function (Builder $query) {

                $query->where('total_stock', ">", 10);
            })
            ->latest("id")
            ->paginate(10)
            ->withQueryString();

            return $products;
    }
}
