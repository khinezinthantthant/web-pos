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
                                "total_today_sale_total" => round($todayTotal),
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

        public function weeklySale()
        {

                $sales = Voucher::select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(net_total) as total')
                )
                        ->whereBetween('created_at', [
                                now()->startOfWeek(),
                                now()->endOfWeek()
                        ])
                        ->groupBy('date')
                        ->get();
                //weekely
                // $startDate = Carbon::now()->startOfWeek();
                // $endDate = Carbon::now()->endOfWeek();
                // $sales = Voucher::whereBetween('created_at', [$startDate, $endDate])
                //         ->selectRaw("DATE(created_at) as date, SUM(net_total) as total")
                //         ->groupBy("date")
                //         ->orderBy('date')
                //         ->get();

                $count = $sales->pluck("date")->count();

                $max = $sales->max("total");
                $highestSaleDate = $sales->where('total', $max)->pluck('date')->first();
                $highestSale[] = [
                        "highest_sale" => round($max,2),
                        "highest_sale_date" => $highestSaleDate
                ];
                // return $highestSaleDate;
                $min = $sales->min("total");
                $lowestSaleDate = $sales->where('total', $min)->pluck('date')->first();
                $lowestSale[] = [
                        "lowest_sale" => round($min,2),
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
                                "total" => $sales->pluck("total")[$i],
                                "dayName" => $dayName[$i],
                                "date" =>  $sales->pluck("date")[$i],
                        ];
                }

                return response()->json([
                        "weekely_sales" => $weekelySales,
                        "TotalWeeklySalesAmount" => round($total,2),
                        "WeeklyAverageAmount" => round($avg, 2),
                        "WeeklyHighestSale" => $highestSale,
                        "WeeklyLowestSale" => $lowestSale,
                ]);
        }

        public function monthlySale()
        {
                //monthly Sale
                $monthlySales = Voucher::select(
                        DB::raw('MONTHNAME(created_at) as month'),
                        // DB::raw('YEAR(created_at) as year'),
                        // DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(net_total) as total')
                )
                        // ->groupBy('year', 'month','date')
                        ->groupBy('month')
                        // ->orderBy('year', 'asc')
                        ->orderBy('month', 'asc')
                        // ->orderBy('date', 'asc')
                        ->get();
                // return $monthlySales;

                $monthlySaleAverage = $monthlySales->avg("total");

                $totalMonthlySale = $monthlySales->sum("total");

                $monthSaleMax = $monthlySales->max("total");
                $highestSaleDate = $monthlySales->where('total', $monthSaleMax)->pluck('month')->first();
                $highestSaleMonth[] = [
                        "highest_sale" => round($monthSaleMax,2),
                        "highest_sale_month" => $highestSaleDate
                ];

                $monthlySaleMin = $monthlySales->min("total");
                // return $monthlySaleMin;
                $monthlyLowestSaleDate = $monthlySales->where('total', $monthlySaleMin)->pluck('month')->first();
                // $monthlyLowestSaleDateFormat = $monthlyLowestSaleDate->format("d-m-Y");
                // return $monthlyLowestSaleDateFormat;
                // $date = Carbon::createFromFormat('F Y', $monthlyLowestSaleDate);
                // return $monthlyLowestSaleDate;

                $lowestSaleMonth[] = [
                        "lowest_sale" => round($monthlySaleMin,2),
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

        public function yearlySale()
        {
                $yearlySales = Voucher::selectRaw('YEAR(created_at) as year,SUM(net_total) as total')
                        ->groupBy('year')
                        ->orderBy('year', 'asc')
                        ->get();
                // return $yearlySales;
                $totalYearlySale = $yearlySales->sum('total');
                $averageYearlySale = $yearlySales->avg('total');

                $yearlyMaxSale = $yearlySales->max('total');
                $highestSaleYear = $yearlySales->where('total', $yearlyMaxSale)->pluck('year')->first();
                $yearlyHighestSale[] = [
                        "highest_sale" => round($yearlyMaxSale,2),
                        "highest_sale_year" => $highestSaleYear
                ];
                // return $yearlyMaxSale;
                $yearlyMinSale = $yearlySales->min('total');
                $lowestSaleYear = $yearlySales->where('total', $yearlyMinSale)->pluck('year')->first();
                $yearlyLowestSale[] = [
                        "lowest_sale" => round($yearlyMinSale,2),
                        "lowest_sale_year" => $lowestSaleYear
                ];

                return response()->json([
                        "yearly_sales" => $yearlySales,
                        "TotalYearlySalesAmount" => round($totalYearlySale,2),
                        "YearlyAverageAmount" => round($averageYearlySale, 2),
                        "YearlyHighestSale" => $yearlyHighestSale,
                        "YearlyLowestSale" => $yearlyLowestSale,
                ]);
        }
        
        
        //     public function weeklySales()
        //     {
        //         // Calculate the start and end dates of the current week
        //         $startDate = now()->startOfWeek();
        //         $endDate = now()->endOfWeek();
        
        //         // Query the database to fetch sales data within the current week
        //         $weeklySales = DB::table('vouchers')
        //             ->whereBetween('created_at', [$startDate, $endDate])
        //             ->get();
        
        //         // Calculate the maximum, minimum, and average sales
        //         $maxSales = $weeklySales->max('total');
        //         $minSales = $weeklySales->min('total');
        //         $avgSales = $weeklySales->avg('total');
        
        //         // Prepare the response
        //         $response = [
        //             'start_date' => $startDate->format('Y-m-d'),
        //             'end_date' => $endDate->format('Y-m-d'),
        //             'sales_data' => $weeklySales,
        //             'max_sales' => $maxSales,
        //             'min_sales' => $minSales,
        //             'avg_sales' => $avgSales,
        //         ];
        
        //         return response()->json($response);
        //     }
        
        
}
