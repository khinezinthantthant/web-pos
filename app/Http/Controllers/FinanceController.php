<?php

namespace App\Http\Controllers;

use App\Http\Resources\DailySaleOverviewResource;
use App\Http\Resources\MonthlySaleOverviewResource;
use App\Http\Resources\MonthlyTotalSaleOverviewResource;
use App\Http\Resources\RecentVoucherResource;
use App\Http\Resources\TodaySaleOverviewResource;
use App\Http\Resources\TodayTotalSaleOverviewResource;
use App\Http\Resources\VoucherResource;
use App\Http\Resources\YearlySaleOverviewResource;
use App\Http\Resources\YearlyTotalSaleOverviewResource;
use App\Models\DailySaleOverview;
use App\Models\MonthlySaleOverview;
use App\Models\SaleClose;
use App\Models\Voucher;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function saleOpen()
    {
        // return "hello";
        $saleClose = SaleClose::find(1);
        if (!$saleClose->sale_close) {
            return response()->json([
                "message" => "Already Opened"
            ]);
        }
        $saleClose->sale_close = false;
        $saleClose->update();

        return response()->json([
            "data" => $saleClose,
            "message" => "Opened Sale"
        ]);
    }
    public function saleClose()
    {
        $saleClose = SaleClose::find(1);
        // return $saleClose;
        if ($saleClose->sale_close) {
            return response()->json([
                "message" => "Already Closed"
            ]);
        }
        $today = Carbon::today();
        $vouchers = Voucher::whereDate("created_at", '=', $today)->get();
        // return $vouchers;
        $totalCash = $vouchers->sum("total");
        $totalTax = $vouchers->sum("tax");
        $total = $vouchers->sum("net_total");
        $totalVouchers = $vouchers->count();
        // return $totalVouchers;
        // $totalCash = Voucher::whereDate('created_at', '=', $today)->sum("total");
        // $totalTax = Voucher::whereDate('created_at', '=', $today)->sum("tax");
        // $total = Voucher::whereDate('created_at', '=', $today)->sum("net_total");
        // $totalVouchers = Voucher::whereDate('created_at', '=', $today)->sum("item_count");
        // $day = Carbon::today()->format("d");
        // $month = Carbon::today()->format("m");
        // $year = Carbon::today()->format("Y");

        $daily_sale_overview = DailySaleOverview::create([
            "total" => $total,
            "total_cash" => $totalCash,
            "total_tax" => $totalTax,
            "total_vouchers" => $totalVouchers,
            // "day" => $day,
            // "month" => $month,
            // "year" => $year
        ]);

        $saleClose->sale_close = true;
        $saleClose->update();


        // return $daily_sale_overview;
        return response()->json([
            "message" => "sale close",
            "data" => $daily_sale_overview
        ]);
    }
    public function monthlyClose(Request $request)
    {
        if (!($request->has("month") && $request->has("year"))) {
            return response()->json([
                "message" => "month and year are required"
            ]);
        }
        try {
            DB::beginTransaction();
            // 
            $saleClose = SaleClose::find(1);
            if ($saleClose->sale_close) {
                return response()->json([
                    "message" => "Already Closed"
                ]);
            }


            $monthly_sale_records = DailySaleOverview::whereMonth("created_at", $request->month)
                ->whereYear("created_at", $request->year)
                ->get();
            // return $monthly_sale_records;

            $total_cash = $monthly_sale_records->sum("total_cash");
            $total_tax = $monthly_sale_records->sum("total_tax");
            $total = $monthly_sale_records->sum("total");
            $total_vouchers = $monthly_sale_records->sum("total_vouchers");

            MonthlySaleOverview::create([
                "total_cash" => $total_cash,
                "total_tax" => $total_tax,
                "total" => $total,
                "total_vouchers" => $total_vouchers,
                "created_at" => Carbon::createFromDate(request()->year,  request()->month, 1)->endOfMonth(),
                "updated_at" => now()
            ]);
            $saleClose->sale_close = true;
            $saleClose->update();

            DB::commit();

            return response()->json([
                "message" => "monthly record saved"
            ], 201);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function recent()
    {
        $today = Carbon::today();
        $todayVoucher = Voucher::whereDate("created_at", $today)->latest("id")->paginate(10)->withQueryString();
        // return $todayVoucher;
        // if($todayVoucher){
        // return response()->json(["message" => "No Recent Voucher"]);
        // }else{
        $total_cash = $todayVoucher->sum("total");
        $total_tax = $todayVoucher->sum("tax");
        $total_vouchers = $todayVoucher->count();
        $total = $todayVoucher->sum("net_total");

        return VoucherResource::collection($todayVoucher)->additional(["total" => [
            "total_voucher" => $total_vouchers,
            "total_cash" => $total_cash,
            "total_tax" => round($total_tax, 2),
            "total" => round($total, 2)
        ]]);
        // }
    }

    public function daily(Request $request)
    {
        $date = Carbon::createFromFormat("d-m-Y", request()->date);
        $daily_sale_records = Voucher::whereDate("created_at", $date)->latest("id")->paginate(10)->withQueryString();
        // return $daily_sale_records;
        $total_cash = $daily_sale_records->sum("total");
        $total_tax = $daily_sale_records->sum("tax");
        $total_vouchers = $daily_sale_records->count();
        $total = $daily_sale_records->sum("net_total");

        return VoucherResource::collection($daily_sale_records)->additional(["total" => [
            "total_voucher" => $total_vouchers,
            "total_cash" => round($total_cash),
            "total_tax" => round($total_tax, 2),
            "total" => $total
        ]]);
    }
    public function monthly(Request $request)
    {
        if (!(request()->has("month") && request()->has("year"))) {
            return response()->json([
                "message" => "month and year are required"
            ]);
        }
        // return $request;
        $monthly_sale_records = DailySaleOverview::whereMonth("created_at", $request->month)
            ->whereYear("created_at", $request->year)
            ->latest("id")->paginate(10)->withQueryString();

        // return $monthly_sale_records;

        $total_cash = $monthly_sale_records->sum("total_cash");
        $total_tax = $monthly_sale_records->sum("total_tax");
        $total = $monthly_sale_records->sum("total");
        $total_vouchers = $monthly_sale_records->sum("total_vouchers");
        

        return MonthlySaleOverviewResource::collection($monthly_sale_records)->additional(["total" => [
            "total_voucher" => $total_vouchers,
            "total_cash" => $total_cash,
            "total_tax" => round($total_tax, 2),
            "total" => round($total, 2)
        ]]);
    }
    public function yearly(Request $request)
    {
        $yearly_sale_records = MonthlySaleOverview::whereYear("created_at", $request->year)
            ->get();

        // return $yearly_sale_records;

        $total_cash = $yearly_sale_records->sum("total_cash");
        $total_tax = $yearly_sale_records->sum("total_tax");
        $total = $yearly_sale_records->sum("total");
        $total_vouchers = $yearly_sale_records->sum("total_vouchers");
        return YearlySaleOverviewResource::collection($yearly_sale_records)->additional(["total" => [
            "total_voucher" => $total_vouchers,
            "total_cash" => $total_cash,
            "total_tax" => round($total_tax, 2),
            "total" => round($total, 2)
        ]]);
    }
    // public function daily(Request $request)
    // {
    //     $year = (new Carbon($request->date))->format("Y");
    //     $month = (new Carbon($request->date))->format("m");

    //     $daily_sale_records = Voucher::whereDate('created_at', '=', $request->date)
    //         // ->whereYear("created_at",$year)
    //         ->paginate(5);
    //     // return $daily_sale_records;
    //     $date = $request->date;
    //     // return $date;
    //     $day = (new Carbon($date))->format("d");
    //     // return $day;

    //     $dailyReport = DailySaleOverview::where('day', '=', $day)
    //         ->where("month", $month)
    //         ->where("year", $year)
    //         ->first();
    //     return $dailyReport;
    //     $dailyReport->daily_sale_records  = $daily_sale_records;

    //     // return $dailyReport;
    //     return new TodayTotalSaleOverviewResource($dailyReport);
    // }

    // public function monthly(Request $request)
    // {
    //     // $monthly_sale_records = DailySaleOverview::where('month', $request->month)
    //     // ->whereYear('created_at', date('Y'))
    //     // ->get();

    //     $monthly_sale_records = DailySaleOverview::where('month', $request->month)
    //         ->where('year', $request->year)
    //         ->get();

    //     return new MonthlyTotalSaleOverviewResource($monthly_sale_records);
    // }

    // public function yearly(Request $request)
    // {
    //     $months = DailySaleOverview::where('year', $request->year)
    //         ->get()->groupBy("month");

    //     if (MonthlySaleOverview::count() <= 0) {
    //         foreach ($months as $key => $month) {
    //             $total_vouchers = $month->sum("total_vouchers");
    //             $total_cash = $month->sum("total_cash");
    //             $total_tax = $month->sum("total_tax");
    //             $total = $month->sum("total");
    //             $month = $key;
    //             $year = $request->year;

    //             $yearly_sale_overviews = MonthlySaleOverview::create([
    //                 "id" => rand(1, 12),
    //                 "total_vouchers" => $total_vouchers,
    //                 "total_cash" => $total_cash,
    //                 "total_tax" => $total_tax,
    //                 "total" => $total,
    //                 "month" => $month,
    //                 "year" => $year
    //             ]);

    //             // return new YearlyTotalSaleOverviewResource($yearly_sale_overviews);
    //         }
    //     } else {
    //         MonthlySaleOverview::truncate();
    //     }

    //     // return $yearly_sale_overviews;
    //     // return new YearlyTotalSaleOverviewResource($yearly_sale_overviews);

    //     return response()->json([
    //         "yearly_total_sale_overview" => new YearlyTotalSaleOverviewResource($yearly_sale_overviews),
    //         "yearly_sale_overviews" => MonthlySaleOverview::all()
    //     ]);
    // }

    // public function customSaleRecords(Request $request)
    // {

    //     $startDate = $request->start_date . " 00::00::00";
    //     // return $startDate;
    //     $endDate = $request->end_date . " 23::59::59";
    //     $custom_sale_records = Voucher::whereBetween('created_at', [$startDate, $endDate])->get();

    //     return TodaySaleOverviewResource::collection($custom_sale_records);
    // }
    public function customSaleRecords()
    {
        if (!request()->has("start_date") && !request()->has("end_date")) {
            return response()->json(["message" => "start date and end date are required"], 400);
        }

        $startDate = Carbon::createFromFormat("d-m-Y", request()->start)->subDay(1);
        $endDate = Carbon::createFromFormat("d-m-Y", request()->end);

        $custom_sale_records = Voucher::whereBetween("created_at", [$startDate, $endDate])->latest("id")->paginate(10)->withQueryString();

        // return $custom_sale_records;
        $total_cash = $custom_sale_records->sum("total");
        $total_tax = $custom_sale_records->sum("tax");
        $total = $custom_sale_records->sum("net_total");
        $total_vouchers = $custom_sale_records->sum("total_vouchers");

        return VoucherResource::collection($custom_sale_records)->additional(["total" => [
            "total_voucher" => $total_vouchers,
            "total_cash" => round($total_cash,2),
            "total_tax" => round($total_tax,2),
            "total" => $total
        ]]);
    }

    public function year()
    {
        return collect(DailySaleOverview::groupBy("year")->get("year"))->pluck("year");
    }
}
