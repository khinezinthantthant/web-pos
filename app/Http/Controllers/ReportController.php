<?php

namespace App\Http\Controllers;

use App\Http\Resources\SaleResource;
use App\Http\Resources\StockReportResource;
use App\Http\Resources\StockResource;
use App\Http\Resources\TodaySaleProductResource;
use App\Http\Resources\WeekelySaleResource;
use App\Models\Brand;
use App\Models\DailySaleOverview;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherRecord;
use Carbon\Carbon;
use DateTime;
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
        $today = Carbon::today();
        $vouchers = Voucher::whereDate("created_at", $today)
            ->orderBy("net_total", "desc")
            ->limit(3)
            ->get();
        // return $vouchers;
        $total_amount = $vouchers->sum("net_total");
        $todaySale = [];

        $total_amount = $vouchers->sum("net_total");
        $todaySale = [];
        foreach ($vouchers as $voucher) {
            $voucher_number = $voucher->voucher_number;
            $total = ($voucher->net_total);
            $percentage = round(($total / $total_amount) * 100, 1) . "%";
            $todaySale[] = [
                "voucher_number" => $voucher_number,
                "total" => $total,
                "percentage" => $percentage
            ];
        }
        return response()->json([
            "total_amount" => $total_amount,
            "todaySale" => $todaySale
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
        //start seeding
        // $total = rand(2, 10) * 100;
        // $tax = $total * 0.05;
        // $net_total = $total + $tax;
        // $startDate = Carbon::now()->subDay(7);
        // $endDate = Carbon::now();

        // $currentDate = $startDate->copy();

        // while ($currentDate->lte($endDate)) {
        //     $voucher = Voucher::factory()->create();
        //     $total_cost = $voucher->voucher_records()->sum('cost');
        //     $voucher->net_total = $total_cost;
        //     $voucher->customer_name = fake()->name();
        //     $voucher->phone_number = fake()->phoneNumber();
        //     $voucher->voucher_number = fake()->regexify('[A-Z0-9]{8}');
        //     $voucher->total = $total;
        //     $voucher->tax = $tax;
        //     $voucher->net_total = $net_total;
        //     $voucher->user_id = rand(1, 3);
        //     $voucher->created_at = $currentDate->toDateString();
        //     $voucher->updated_at = $currentDate->toDateString();
        //     $voucher->update();
        //     $currentDate->addDay();
        // }

        // //end seeding

        // // weekely sale
        // $date = Carbon::now()->subDays(7);

        // $sales = Voucher::where('created_at', '>=', $date)
        //         ->selectRaw("DATE(created_at) as date, SUM(net_total) as net_total")
        //         ->groupBy("date")
        //         ->orderBy('date')
        //         ->get();

        // $count = $sales->pluck("date")->count();

        // $total = $sales->sum("net_total");
        // $max = $sales->max("net_total");
        // $highestSaleDate = $sales->where("net_total",$max)->pluck("date")->first();
        // $highestPercentage = round(($max / $total) * 100 ,1) ."%";

        // $highestSale[] = [
        //     "highest_sale" => $max,
        //     "highest_sale_date" => $highestSaleDate,
        //     "dayName" => Carbon::parse($highestSaleDate)->format("D"),
        //     "highestPercentage" => $highestPercentage
        // ];

        // // return $highestSale;

        // $min = $sales->min("net_total");
        // $lowestSaleDate = $sales->where("net_total",$min)->pluck("date")->first();
        // $lowestPercentage = round(($min / $total) * 100 ,1) ."%";

        // $lowestSale[] = [
        //     "lowest_sale" => $min,
        //     "lowest_sale_date" => $lowestSaleDate,
        //     "dayName" => Carbon::parse($lowestSaleDate)->format("D"),
        //     "lowestPercentage" => $lowestPercentage
        // ];

        // // return $lowestSale;

        // $avg =$sales->avg("net_total");

        // $date = [];
        // $dayName = [];
        // $weekelySales = [];

        // for ($i = 0; $i < $count; $i++) {
        //     $date[] = Carbon::parse($sales->pluck("date")[$i]);
        //     $dayName[] = $date[$i]->format("D");

        //     $weekelySales[] =  [
        //             "total" => $sales->pluck("net_total")[$i],
        //             "dayName" => $dayName[$i],
        //             "date" =>  $sales->pluck("date")[$i],
        //     ];
        // }

        // return response()->json([
        //     "weekely_sales" => $weekelySales,
        //     "total_weekely_sale_amount" => round($total,2),
        //     "weekely_average_amount" => round($avg,2),
        //     "weekely_highest_sale" => $highestSale,
        //     "weekely_lowest_sale" => $lowestSale
        // ]);




        $startOfDay =  Carbon::now()->startOfWeek();
        // return $startOfDay;
        $endOfDay = $startOfDay->copy()->endOfWeek();
        // return $endOfDay;
        $totalSale = DailySaleOverview::whereBetween('created_at', [$startOfDay, $endOfDay])->get();

        // return $totalSale;
        $total = $totalSale->sum('total');
        // return $total;

        $maxWeekelySale = $totalSale->where('total', $totalSale->max('total'))->first();
        // return $maxWeekelySale;
        $maxPrice = $maxWeekelySale->max("total");
        // return $maxPrice;
        $max = new SaleResource($maxWeekelySale);
        $maxPercentage = round(($maxPrice / $total) * 100, 1) . "%";
        // return $maxPercentage;


        $averageWeeklySale = $totalSale->avg('total');
        $minWeeklySale = $totalSale->where('total', $totalSale->min('total'))->first();
        // return $minWeeklySale;
        $minPrice = $minWeeklySale->min("total");
        // return $minPrice;
        $minPercentage = round(($minPrice / $total) * 100, 1) . "%";
        // return $minPercentage;
        $min = new SaleResource($minWeeklySale);
        $totalSale = WeekelySaleResource::collection($totalSale);
        // return $totalSale;

        return response()->json([
            "weekly_sale_total" => round($total, 2),
            "weekly_highest_sale" => $max,
            "weekly_highest_percentage" => $maxPercentage,

            "weekly_lowest_sale" => $min,
            "weekly_lowest_percentage" => $minPercentage,
            "average" => round($averageWeeklySale, 2),
            "weekly_sale" => $totalSale
        ]);
    }

    public function monthlySaleReport()
    {
        // my code start
        // $monthlySales = Voucher::whereBetween('created_at', [
        //     Carbon::now()->startOfYear(),
        //     Carbon::now()->endOfYear(),
        // ])
        // ->select(
        //     DB::raw('MONTHNAME(created_at) as month'),
        //     DB::raw('YEAR(created_at) as year'),
        //     DB::raw('SUM(net_total) as total')
        // )
        // ->groupBy("month","year")

        // ->get();

        // $monthlySaleAverage = $monthlySales->avg("total");
        // $totalMonthlySale = $monthlySales->sum("total");
        // $monthlySaleMax = $monthlySales->max("total");
        // $highestSaleDate = $monthlySales->where('total', $monthlySaleMax)->pluck('month')->first();

        // $highestPercentage = round(($monthlySaleMax / $totalMonthlySale) * 100, 1) . "%";


        // $highestSaleMonth[] = [
        //     "highest_sale" => $monthlySaleMax,
        //     "highest_sale_month" => $highestSaleDate,
        //     "highest_percentage" => $highestPercentage
        // ];

        // $monthlySaleMin = $monthlySales->min("total");
        // // return $monthlySaleMin;

        // $monthlyLowestSaleDate = $monthlySales->where('total', $monthlySaleMin)->pluck('month')->first();
        // // return $monthlyLowestSaleDate;

        // $lowestPercentage = round(($monthlySaleMin / $totalMonthlySale) * 100, 1) . "%";


        // $lowestSaleMonth[] = [
        //     "lowest_sale" => $monthlySaleMin,
        //     "lowest_sale_month" => $monthlyLowestSaleDate,
        //     "lowest_percentage" => $lowestPercentage
        //     ];

        //     return response()->json([
        //         "monthly_sales" => $monthlySales,
        //         "TotalMonthlySalesAmount" => round($totalMonthlySale,2),
        //         "MonthlyAverageAmount" => round($monthlySaleAverage, 2),
        //         "MonthlyHighestSale" => $highestSaleMonth,
        //         "MonthlyLowestSale" => $lowestSaleMonth,

        // ]);

        // my code end


        //monthly Sale
        $monthlySales = Voucher::whereBetween('created_at', [
            Carbon::now()->startOfYear(),
            Carbon::now()->endOfYear(),
        ])
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(net_total) as total')
            )
            ->groupBy('month', 'year')
            ->orderBy('month', 'asc')
            ->get();

        $formatedMonthlySales = $monthlySales->map(function ($item) {
            $dateObj = DateTime::createFromFormat('!m', $item->month);
            $monthName = $dateObj->format("M");

            return [
                'month' => $monthName,
                'year' => $item->year,
                'total' => $item->total
            ];
        });
        $monthlySaleAverage = $monthlySales->avg("total");

        $totalMonthlySale = $monthlySales->sum("total");

        $monthSaleMax = $monthlySales->max("total");
        $highestSaleDate = $formatedMonthlySales->where('total', $monthSaleMax)->pluck('month')->first();

        $highestPercentage = round(($monthSaleMax / $totalMonthlySale) * 100, 1) . "%";

        $highestSaleMonth[] = [
            "highest_sale" => round($monthSaleMax, 2),
            "highest_sale_month" => $highestSaleDate,
            "percentage" => $highestPercentage
        ];

        $monthlySaleMin = $monthlySales->min("total");
        $monthlyLowestSaleDate = $formatedMonthlySales->where('total', $monthlySaleMin)->pluck('month')->first();
        $lowestPercentage = round(($monthlySaleMin / $totalMonthlySale) * 100, 1) . "%";
        $lowestSaleMonth[] = [
            "lowest_sale" => round($monthlySaleMin, 2),
            "lowest_sale_month" => $monthlyLowestSaleDate,
            "percentage" => $lowestPercentage
        ];
        $date = [];
        $dayName = [];

        return response()->json([
            "monthly_sales" => $formatedMonthlySales,
            "TotalMonthlySalesAmount" => round($totalMonthlySale, 2),
            "MonthlyAverageAmount" => round($monthlySaleAverage, 2),
            "MonthlyHighestSale" => $highestSaleMonth,
            "MonthlyLowestSale" => $lowestSaleMonth,

        ]);
    }

    public function yearlySaleReport()
    {
        $yearlySales = Voucher::selectRaw('YEAR(created_at) as year, SUM(net_total) as total')
            ->groupBy("year")
            ->orderBy("year", "asc")
            ->get();
        $totalYearlySale = $yearlySales->sum("total");
        $averageYearlySale = $yearlySales->avg("total");
        $yearlyMaxSale = $yearlySales->max("total");
        $highestSaleYear = $yearlySales->where("total", $yearlyMaxSale)->pluck("year")->first();

        $highestPercentage = round(($yearlyMaxSale / $totalYearlySale) * 100, 1) . "%";

        $yearlyHighestSale[] = [
            "highest_sale" => $yearlyMaxSale,
            "highest_sale_year" => $highestSaleYear,
            "highest_percentage" => $highestPercentage
        ];

        $yearlyMinSale = $yearlySales->min("total");
        $lowestSaleYear = $yearlySales->where("total", $yearlyMinSale)->pluck("year")->first();
        $lowestPercentage = round(($yearlyMinSale / $totalYearlySale) * 100, 1) . "%";
        $yearlyLowestSale[] = [
            "lowest_sale" => $yearlyMinSale,
            "lowest_sale_year" => $lowestSaleYear,
            "lowest_percentage" => $lowestPercentage
        ];

        return response()->json([
            "yearly_sales" => $yearlySales,
            "total_yearly_sales_amount" => round($totalYearlySale, 2),
            "yearly_average_amount" => round($averageYearlySale, 2),
            "yearly_highest_sale" => $yearlyHighestSale,
            "yearly_lowest_sale" => $yearlyLowestSale,
        ]);
    }


    public function overview()
    {
        $totalStock = Stock::all()->sum("quantity"); 
        $totalStaff = User::all()->count();

        $yearlySales = Voucher::selectRaw('YEAR(created_at) as year,SUM(total) as total')
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->get();

        //monthly sales

        $monthlySales = Voucher::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(total) as total')
        )
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $monthlySales = Voucher::whereBetween('created_at', [
            Carbon::now()->startOfYear(),
            Carbon::now()->endOfYear(),
        ])
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(net_total) as total')
            )
            ->groupBy('month', 'year')
            ->orderBy('month', 'asc')
            ->get();

        $formatedMonthlySales = $monthlySales->map(function ($item) {
            $dateObj = DateTime::createFromFormat('!m', $item->month);
            $monthName = $dateObj->format("M");

            return [
                'month' => $monthName,
                'year' => $item->year,
                'total' => round($item->total,2)
            ];
        });

        // weekelySales

        $startOfDay =  Carbon::now()->startOfWeek();
        $endOfDay = $startOfDay->copy()->endOfWeek();
        $totalSale = DailySaleOverview::whereBetween('created_at', [$startOfDay, $endOfDay])->get();

        $totalSale = WeekelySaleResource::collection($totalSale);

        return response()->json([
            "total_stocks" => $totalStock,
            "total_staff" => $totalStaff,
            "yearly_sales" => $yearlySales,
            "monthly_sales" => $formatedMonthlySales,
            "weekely_sales" => $totalSale,
        ]);
    }
}
