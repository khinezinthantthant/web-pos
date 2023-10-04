<?php

namespace App\Http\Controllers;

use App\Http\Resources\StockReportResource;
use App\Http\Resources\StockResource;
use App\Http\Resources\TodaySaleProductResource;
use App\Models\Brand;
use App\Models\DailySaleOverview;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Voucher;
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
        $inStock = Product::where("total_stock", ">", 10)->count("id");

        return response()->json([
            "totalProducts" => $totalProducts,
            "totalBrands" => $totalBrands,
            "stocks" => [
                "inStock" => ($inStock / $totalProducts) * 100 . "%",
                "lowStock" => ($lowStock / $totalProducts) * 100 . "%",
                "outOfStock" => ($outOfStock / $totalProducts) * 100 . "%",
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
        foreach ($brands as $brandId => $brandName) {
            $saleBrand = VoucherRecord::whereBetween("created_at", [$startDate, $endDate])
                ->whereHas("product", function ($query) use ($brandId) {
                    $query->where("brand_id", $brandId);
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

    public function todaySaleReport()
    {
            $todayStart = Carbon::now()->format('Y-m-d 00:00:00');
            $todayEnd = Carbon::now()->format('Y-m-d 23:59:59');
            $todaySaleProduct = Voucher::whereBetween('created_at', [$todayStart, $todayEnd])
                    ->get();
            $todayTotal = $todaySaleProduct->sum('total');
            $todaySaleMax = $todaySaleProduct->max("total");
            $todaySaleMin = $todaySaleProduct->min("total");
            $todaySaleAvg = $todaySaleProduct->avg("total");

            // return response()->json([
            //         "today_sales" => [
            //                 "total_today_sale_amount" => round($todayTotal),
            //                 "today_avg_sale" => round($todaySaleAvg),
            //                 "today_max_sale" => new TodaySaleProductResource($todaySaleMax),
            //                 "today_min_sale" => new TodaySaleProductResource($todaySaleMin),
            //         ]
            // ]);

            return response()->json([
                "total_today_sale_amount" => round($todayTotal),
                            "today_avg_sale" => round($todaySaleAvg),
                            "today_max_sale" => $todaySaleMax,
                            "today_min_sale" => $todaySaleMin,
            ]);
    }

    public function productSaleReport()
    {
            $products = Product::withCount("voucher_records")->orderByDesc("voucher_records_count")
                    ->limit(5)->get();
            // return $products;
            $productInfo = [];
            foreach ($products as $product) {
                    $productName = $product->name;
                    $brandName = $product->brand->name;
                    $porductPrice = $product->sale_price;
                    $unit = $product->unit;
                    $totalVoucher = $product->voucher_records_count;
                    $totalStock = $product->total_stock;

                    $productInfo[] = [
                            "name" => $productName,
                            "brand" => $brandName,
                            "sale_price" => $porductPrice,
                            "unit" => $unit,
                            "total_stock" => $totalStock,
                            "total_voucher" => $totalVoucher
                    ];
            }
            return response()->json([
                    "productInfo" => $productInfo,
            ]);
    }

    public function brandSaleReport()
    {
        $brands = Brand::withCount("brands")
                ->orderByDesc("brands_count")
                ->withSum("brands", "cost")
                // ->withSum("brands", "quantity")
                ->limit(5)
                ->get();
        $brandInfo = [];

        foreach ($brands as $brand) {
                $brandName = $brand->name;
                $brandSaleCount = $brand->brands_count;
                $brandSales = $brand->brands_sum_cost;

                $brandInfo[] = [
                        "name" => $brandName,
                        "brand_sale_count" => $brandSaleCount,
                        "brand_sales" => $brandSales,
                ];
        }

        return response()->json([
                'brandsInfo' => $brandInfo
        ]);
    }

    public function weeklySaleReport()
    {
        // weekely sale
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();
        $sales = Voucher::whereBetween("created_at",[$startDate,$endDate])
                ->selectRaw("Date(created_at) as date, SUM(net_total) as total")
                ->groupBy("date")
                ->orderBy("date")
                ->get();

        $count = $sales->pluck("date")->count();

        $max = $sales->max("total");
        $highestSaleDate = $sales->where("total",$max)->pluck("date")->first();

        $highestSale[] = [
            "highest_sale" => $max,
            "highest_sale_date" => $highestSaleDate,
            "dayName" => Carbon::parse($highestSaleDate)->format("l")
        ];

        $min = $sales->min("total");
        $lowestSaleDate = $sales->where("total",$min)->pluck("date")->first();

        $lowestSale[] = [
            "lowest_sale" => $min,
            "lowest_sale_date" => $lowestSaleDate,
            "dayName" => Carbon::parse($lowestSaleDate)->format("l")
        ];

        $total = $sales->sum("total");
        $avg =$sales->avg("total");

        $date = [];
        $dayName = [];
        $weekelySales = [];

        for ($i = 0; $i < $count; $i++) {
            $date[] = Carbon::parse($sales->pluck("date")[$i]);
            $dayName[] = $date[$i]->format("l");

            $weekelySales[] =  [
                    "total" => $sales->pluck("total")[$i],
                    "dayName" => $dayName[$i],
                    "date" =>  $sales->pluck("date")[$i],
            ];
        }

        return response()->json([
            "weekely_sales" => $weekelySales,
            "total_weekely_sale_amount" => round($total,2),
            "weekely_average_amount" => round($avg,2),
            "weekely_highest_sale" => $highestSale,
            "weekely_lowest_sale" => $lowestSale
        ]);


    }

    public function monthlySaleReport()
    {
        // monthly sale
        $monthlySales = Voucher::whereBetween('created_at', [
            Carbon::now()->startOfYear(),
            Carbon::now()->endOfYear(),
        ])
        ->select(
            DB::raw('MONTHNAME(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(net_total) as total')
        )
        ->groupBy("month","year")

        ->get();
        $monthlySaleAverage = $monthlySales->avg("total");
        $totalMonthlySale = $monthlySales->sum("total");
        $monthlySaleMax = $monthlySales->max("total");
        $highestSaleDate = $monthlySales->where('total', $monthlySaleMax)->pluck('month')->first();

        $highestSaleMonth[] = [
            "highest_sale" => $monthlySaleMax,
            "highest_sale_month" => $highestSaleDate
        ];

        $monthlySaleMin = $monthlySales->min("total");
        // return $monthlySaleMin;

        $monthlyLowestSaleDate = $monthlySales->where('total', $monthlySaleMin)->pluck('month')->first();
        // return $monthlyLowestSaleDate;

        $lowestSaleMonth[] = [
            "lowest_sale" => $monthlySaleMin,
            "lowest_sale_month" => $monthlyLowestSaleDate
            ];

            return response()->json([
                "monthly_sales" => $monthlySales,
                "TotalMonthlySalesAmount" => round($totalMonthlySale,2),
                "MonthlyAverageAmount" => round($monthlySaleAverage, 2),
                "MonthlyHighestSale" => $highestSaleMonth,
                "MonthlyLowestSale" => $lowestSaleMonth,

        ]);

    }

    public function yearlySaleReport()
    {
        $yearlySales = Voucher::selectRaw('YEAR(created_at) as year, SUM(net_total) as total')
                    ->groupBy("year")
                    ->orderBy("year","asc")
                    ->get();
        $totalYearlySale = $yearlySales->sum("total");
        $averageYearlySale = $yearlySales->avg("total");
        $yearlyMaxSale = $yearlySales->max("total");
        $highestSaleYear = $yearlySales->where("total", $yearlyMaxSale)->pluck("year")->first();
        $yearlyHighestSale[] = [
            "highest_sale" => $yearlyMaxSale,
            "highest_sale_year" => $highestSaleYear
        ];

        $yearlyMinSale = $yearlySales->min("total");
        $lowestSaleYear = $yearlySales->where("total", $yearlyMinSale)->pluck("year")->first();
        $yearlyLowestSale[] = [
            "lowest_sale" => $yearlyMinSale,
            "lowest_sale_year" => $lowestSaleYear
        ];

        return response()->json([
            "yearly_sales" => $yearlySales,
            "total_yearly_sales_amount" => round($totalYearlySale,2),
            "yearly_average_amount" => round($averageYearlySale, 2),
            "yearly_highest_sale" => $yearlyHighestSale,
            "yearly_lowest_sale" => $yearlyLowestSale,
        ]);

    }

}
