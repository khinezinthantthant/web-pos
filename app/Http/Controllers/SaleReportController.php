<?php

namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;

use App\Http\Resources\ProductReportResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\TodaySaleProductResource;
use App\Http\Resources\weeklySaleProductResource;
use App\Models\Brand;
use App\Models\DailySaleOverview;
use App\Models\MonthlySaleOverview;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\VoucherRecord;
use Carbon\Carbon;
use DateTime;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class SaleReportController extends Controller
{

        public function todaySaleReport()
        {
                $todayStart = Carbon::now()->format('Y-m-d 00:00:00');
                $todayEnd = Carbon::now()->format('Y-m-d 23:59:59');
                $todaySaleProduct = Voucher::whereBetween('created_at', [$todayStart, $todayEnd])
                        // ->orderBy('net_total', 'DESC')
                        ->get();
                // return $todaySaleProduct;

                $todayTotal = $todaySaleProduct->sum('net_total');
                $todaySaleMax = $todaySaleProduct->where("net_total", $todaySaleProduct->max("net_total"))->first();
                // return $todaySaleMax;
                $todaySaleMin = $todaySaleProduct->where("net_total", $todaySaleProduct->min("net_total"))->first();
                // return $todaySaleMin;
                $todaySaleAvg = $todaySaleProduct->avg("net_total");
                // return $todaySaleAvg;

                return response()->json([
                        "today_sales" => [
                                "total_today_sale_amount" => round($todayTotal),
                                "today_avg_sale" => round($todaySaleAvg),
                                "today_max_sale" => new TodaySaleProductResource($todaySaleMax),
                                "today_min_sale" => new TodaySaleProductResource($todaySaleMin),
                        ]
                ]);
        }
        public function productSale()
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
                                "totoal_voucher" => $totalVoucher

                        ];
                }
                // return $productInfo;
                return response()->json([
                        "productInfo" => $productInfo,
                ]);
        }
        public function brandSale()
        {
                $brands = Brand::withCount("brands")
                        ->orderByDesc("brands_count")
                        ->withSum("brands", "cost")
                        // ->withSum("brands", "quantity")
                        ->limit(5)
                        ->get();
                // return $brands;
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

        public function saleOverview()
        {
                $yearlySales = Voucher::selectRaw('YEAR(created_at) as year,SUM(total) as total')
                        ->groupBy('year')
                        ->orderBy('year', 'asc')
                        ->get();
                        // return $yearlySales;
                $totalYearlySale = $yearlySales->sum('total');
                $averageYearlySale = $yearlySales->avg('total');

                $yearlyMaxSale = $yearlySales->max('total');
                $highestSaleYear = $yearlySales->where('total', $yearlyMaxSale)->pluck('year')->first();
                $yearlyHighestSale[] = [
                        "highest_sale" => $yearlyMaxSale,
                        "highest_sale_year" => $highestSaleYear
                ];
                // return $yearlyMaxSale;
                $yearlyMinSale = $yearlySales->min('total');
                $lowestSaleYear = $yearlySales->where('total', $yearlyMinSale)->pluck('year')->first();
                $yearlyLowestSale[] = [
                        "lowest_sale" => $yearlyMinSale,
                        "lowest_sale_year" => $lowestSaleYear
                ];

                //monthly Sale
                $monthlySales = Voucher::select(
                        DB::raw('MONTH(created_at) as month'),
                        // DB::raw('YEAR(created_at) as year'),
                        // DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(total) as total')
                )
                        // ->groupBy('year', 'month','date')
                        ->groupBy('month')
                        // ->orderBy('year', 'asc')
                        ->orderBy('month', 'asc')
                        // ->orderBy('date', 'asc')
                        ->get();
                // return $monthlySales;
               
                $formatedMonthlySales = $monthlySales->map(function ($item) {
                        $dateObj = DateTime::createFromFormat('!m', $item->month);
                        $monthName = $dateObj->format("F");
                        // return $item;
                        // $monthlySaleMin = $item->min("total");
                        // return $monthlySaleMin;
                        // $monthlyLowestSaleDate = $item->where('total', $monthlySaleMin)->pluck('created_at')->first();
                        // $monthlyLowestSaleDateFormat = $monthlyLowestSaleDate->format("d-m-Y");
                        // return $monthlyLowestSaleDate;

                        return [
                                'month' => $monthName,
                                // 'year' => $item->year,
                                'total' => $item->total,
                        ];
                });
                // return $formatedMonthlySales;
                $monthlySaleAverage = $formatedMonthlySales->avg("total");

                $totalMonthlySale = $formatedMonthlySales->sum("total");

                $monthSaleMax = $formatedMonthlySales->max("total");
                $highestSaleDate = $formatedMonthlySales->where('total', $monthSaleMax)->pluck('month')->first();
                $highestSaleMonth[] = [
                        "highest_sale" => $monthSaleMax,
                        "highest_sale_month" => $highestSaleDate
                ];

                $monthlySaleMin = $formatedMonthlySales->min("total");
                // return $monthlySaleMin;
                $monthlyLowestSaleDate = $formatedMonthlySales->where('total', $monthlySaleMin)->pluck('month')->first();
                // $monthlyLowestSaleDateFormat = $monthlyLowestSaleDate->format("d-m-Y");
                // return $monthlyLowestSaleDateFormat;
                // return $monthlyLowestSaleDate;
                $lowestSaleMonth[] = [
                        "lowest_sale" => $monthlySaleMin,
                        "lowest_sale_month" => $monthlyLowestSaleDate
                ];

                //weekely
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                $sales = Voucher::whereBetween('created_at', [$startDate, $endDate])
                        ->selectRaw("DATE(created_at) as date, SUM(total) as total")
                        ->groupBy("date")
                        ->orderBy('date')
                        ->get();

                $count = $sales->pluck("date")->count();

                $max = $sales->max("total");
                $highestSaleDate = $sales->where('total', $max)->pluck('date')->first();
                $highestSale[] = [
                        "highest_sale" => $max,
                        "highest_sale_date" => $highestSaleDate
                ];
                // return $highestSaleDate;
                $min = $sales->min("total");
                $lowestSaleDate = $sales->where('total', $min)->pluck('date')->first();
                $lowestSale[] = [
                        "lowest_sale" => $min,
                        "lowest_sale_date" => $lowestSaleDate
                ];
                // return $lowestSaleDate;

                $total = $sales->sum("total");
                $avg = $sales->avg("total");

                $date = [];
                $dayName = [];

                $weekelySales = [];
                for ($i = 0; $i < $count; $i++) {
                        $date[] = Carbon::parse($sales->pluck("date")[$i]);
                        $dayName[] = $date[$i]->format("l");

                        $weekelySales[] =  [
                                "totalDaySale" => $sales->pluck("total")[$i],
                                "dayName" => $dayName[$i],
                                "date" =>  $sales->pluck("date")[$i],
                        ];
                }

                return response()->json([
                        "yearly_sales" => $yearlySales,
                        "TotalYearlySalesAmount" => $totalYearlySale,
                        "YearlyAverageAmount" => round($averageYearlySale, 2),
                        "YearlyHighestSale" => $yearlyHighestSale,
                        "YearlyLowestSale" => $yearlyLowestSale,

                        "monthly_sales" => $formatedMonthlySales,
                        "TotalMonthlySalesAmount" => $totalMonthlySale,
                        "MonthlyAverageAmount" => round($monthlySaleAverage, 2),
                        "MonthlyHighestSale" => $highestSaleMonth,
                        "MonthlyLowestSale" => $lowestSaleMonth,

                        "weekely_sales" => $weekelySales,
                        "TotalWeeklySalesAmount" => $total,
                        "WeeklyAverageAmount" => round($avg, 2),
                        "WeeklyHighestSale" => $highestSale,
                        "WeeklyLowestSale" => $lowestSale,
                ]);
        }

        public function weeklySaleTest()
        {
                $records = DB::table('daily_sale_overviews')
                        ->select([
                                // DB::raw('DAYOFWEEK(created_at) as day_of_week_numeric'),
                                DB::raw('CASE
            WHEN DAYOFWEEK(created_at) = 1 THEN "Sunday"
            WHEN DAYOFWEEK(created_at) = 2 THEN "Monday"
            WHEN DAYOFWEEK(created_at) = 3 THEN "Tuesday"
            WHEN DAYOFWEEK(created_at) = 4 THEN "Wednesday"
            WHEN DAYOFWEEK(created_at) = 5 THEN "Thursday"
            WHEN DAYOFWEEK(created_at) = 6 THEN "Friday"
            WHEN DAYOFWEEK(created_at) = 7 THEN "Saturday"
            ELSE "Unknown"
        END as day_of_week_name')

                        ])
                        ->get();
                return $records;
        }
        
}
