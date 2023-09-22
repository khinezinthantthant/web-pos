<?php

namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;

use App\Http\Resources\ProductReportResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\TodaySaleProductResource;
use App\Http\Resources\weeklySaleProductResource;
use App\Models\Brand;
use App\Models\DailySaleOverview;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\VoucherRecord;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
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





        public function weeklySale()
        {
                $now = Carbon::now();

                $weeklySale = Voucher::whereBetween("created_at", [
                        $now->startOfWeek()->format('Y-m-d'), //This will return date in format like this: 2022-01-10
                        $now->endOfWeek()->format('Y-m-d')
                ])->get();
                // return $weeklySale;
                $max = $weeklySale->max("net_total");
                $min = $weeklySale->min("net_total");
                $avgSale = $weeklySale->avg("net_total");
                $avg = round($avgSale, 2);
                $totalWeeklySale = $weeklySale->sum("net_total");
                // return $total;
                // return $avg;
                return response()->json([
                        "totalWeeklySale" => $totalWeeklySale,
                        "maxSale" => $max,
                        "minSale" => $min,
                        "avgSale" => $avg
                ]);
        }

       

        public function week()
        {
                $weeklySales = DB::table('daily_sale_overviews')
                        ->select(
                                DB::raw('CASE DAYOFWEEK(created_at)
                        WHEN 1 THEN "Sunday"
                        WHEN 2 THEN "Monday"
                        WHEN 3 THEN "Tuesday"
                        WHEN 4 THEN "Wednesday"
                        WHEN 5 THEN "Thursday"
                        WHEN 6 THEN "Friday"
                        WHEN 7 THEN "Saturday"
                    END AS day_name'),
                                DB::raw('SUM(total) as total_sale'),
                                // DB::raw('MIN(net_total) as min_sale'),
                                // DB::raw('MAX(net_total) as max_sale'),
                                // DB::raw('AVG(net_total) as avg_sale')
                        )
                        ->groupBy('day_name')
                        ->get();

                $weeklySaleTotal = $weeklySales->sum("total_sale");
                return $weeklySales;
                // $maxSale = $weeklySales->pluck("total_sale");
                $maxSale = $weeklySales->max("total_sale");
                return $maxSale;
                // $bestSellingDate = $weeklySales->where('total_sales', $maxSellingPrice)->pluck('sale_date')->first();
                $highestSaleDay = $weeklySales->where("total_sale", $maxSale)->pluck("day_name")->first();
                // $dayName = ; 
                // return $dayName;
                // return $highestSaleDay;

                return response()->json([
                        // $weeklySales,
                        $weeklySaleTotal
                        // "dayName" => $
                ]);
        }
        public function saleOverview($type)
        {
            $currentDate = Carbon::now();
            $previousDate = '';
            $day = "day";
    
            if ($type == "weekly") {
                $previousDate = Carbon::now()->subDays(7);
            } else if ($type == "monthly") {
                $previousDate = Carbon::now()->subDays(30);
            } else if ($type == "yearly") {
                $previousDate = Carbon::now()->subDays(365);
                $day = "monthly";
            } else {
                return response()->json(["message" => "weekly or monthly or yearly is required"]);
            }
    
            $query = Voucher::whereBetween("created_at", [$previousDate, $currentDate])->where("day", $day);
            $query2 = Voucher::whereBetween("created_at", [$previousDate, $currentDate])->where("day", $day);
    
            $average = $query->avg("total");
    
            $records = $query->select("total", "created_at")->get();
    
            $max = $query->where('total', $query->max('total'))->select("total", "created_at")->first();
    
            $min = $query2->where('total', $query2->min('total'))->select("total", "created_at")->first();
    
            return response()->json([
                "average" => $average,
                "max" => $max,
                "min" => $min,
                "records" => $records
            ]);
        }
}
