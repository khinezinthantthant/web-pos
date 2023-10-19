<?php

namespace App\Http\Controllers;

use App\Http\Resources\MonthlySaleOverviewResource;
use App\Http\Resources\MonthlyTotalSaleOverviewResource;
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
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FinanceController extends Controller
{
    public function saleClose(Request $request)
    {

        $saleClose = SaleClose::find(1);
        if($saleClose->sale_close){
            return response()->json([
                "message" => "Already Closed"
            ]);
        }
        $saleClose->sale_close = true;
        $saleClose->update();


        $totalCash = Voucher::whereDate('created_at', '=', Carbon::today()->toDateString())->sum("total");
        $totalTax = Voucher::whereDate('created_at', '=', Carbon::today()->toDateString())->sum("tax");
        $total = Voucher::whereDate('created_at', '=', Carbon::today()->toDateString())->sum("net_total");
        $totalVouchers = Voucher::whereDate('created_at', '=', Carbon::today()->toDateString())->count("id");
        $day = Carbon::today()->format("d");
        $month = Carbon::today()->format("m");
        $year = Carbon::today()->format("Y");

        $daily_sale_overview = DailySaleOverview::create([
            "total" => $total,
            "total_cash" => $totalCash,
            "total_tax" => $totalTax,
            "total_vouchers" => $totalVouchers,
            "day" => $day,
            "month" => $month,
            "year" => $year
        ]);

        return $daily_sale_overview;
    }

    public function saleOpen()
    {
        // return "hello";
        $saleClose = SaleClose::find(1);
        if(!$saleClose->sale_close){
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

    public function daily(Request $request)
    {
        // $year = (new Carbon($request->date))->format("Y");
        // $month = (new Carbon($request->date))->format("m");

        // $daily_sale_records = Voucher::whereDate('created_at', '=', $request->date)
        //     // ->whereYear("created_at",$year)
        //     ->paginate(10)->withQueryString();
        // $date = $request->date;
        // $day = (new Carbon($date))->format("d");

        // $dailyReport = DailySaleOverview::where('day', '=', $day)
        //     ->where("month", $  )
        //     ->where("year", $year)
        //     ->first();
        // $dailyReport->daily_sale_records  = $daily_sale_records;

        // return $dailyReport;

        // return new TodayTotalSaleOverviewResource($dailyReport);


        $date = Carbon::createFromFormat("d-m-Y", request()->date);
        $daily_sale_records = Voucher::whereDate("created_at", $date)->latest("id")->paginate(10)->withQueryString();
        // return $daily_sale_records;
        $total_cash = $daily_sale_records->sum("total");
        $total_tax = $daily_sale_records->sum("tax");
        $total_vouchers = $daily_sale_records->count();
        $total = $daily_sale_records->sum("net_total");

        return VoucherResource::collection($daily_sale_records)->additional(["total" => [
            "total_voucher" => $total_vouchers,
            "total_cash" => $total_cash,
            "total_tax" => round($total_tax, 2),
            "total" => round($total, 2)
        ]]);
    }

    public function monthly(Request $request)
    {
        // $monthly_sale_records = DailySaleOverview::where('month', $request->month)
        //     ->where('year', $request->year)
        //     ->paginate(10)->withQueryString();

        // return new MonthlyTotalSaleOverviewResource($monthly_sale_records);


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
        // $months = DailySaleOverview::where('year', $request->year)
        //     ->get()->groupBy("month");

        // if (MonthlySaleOverview::count() <= 0) {
        //     foreach ($months as $key => $month) {
        //         $total_vouchers = $month->sum("total_vouchers");
        //         $total_cash = $month->sum("total_cash");
        //         $total_tax = $month->sum("total_tax");
        //         $total = $month->sum("total");
        //         $month = $key;
        //         $year = $request->year;

        //         $yearly_sale_overviews = MonthlySaleOverview::create([
        //             "id" => rand(1, 12),
        //             "total_vouchers" => $total_vouchers,
        //             "total_cash" => $total_cash,
        //             "total_tax" => $total_tax,
        //             "total" => $total,
        //             "month" => $month,
        //             "year" => $year
        //         ]);

        //         // return new YearlyTotalSaleOverviewResource($yearly_sale_overviews);
        //     }

        // } else {
        //     MonthlySaleOverview::truncate();
        // }

        // // return $yearly_sale_overviews;
        // // return new YearlyTotalSaleOverviewResource($yearly_sale_overviews);

        // return response()->json([
        //     "yearly_total_sale_overview" => new YearlyTotalSaleOverviewResource($yearly_sale_overviews),
        //     "yearly_sale_overviews" => MonthlySaleOverview::paginate(10)->withQueryString()
        // ]);


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

    public function customSaleRecords(Request $request)
    {

        // $startDate = $request->start_date . " 00::00::00";
        // // return $startDate;
        // $endDate = $request->end_date . " 23::59::59";
        // $custom_sale_records = Voucher::whereBetween('created_at', [$startDate, $endDate])->paginate(10)->withQueryString();

        // return TodaySaleOverviewResource::collection($custom_sale_records);


        if (!request()->has("start_date") && !request()->has("end_date")) {
            return response()->json(["message" => "start date and end date are required"], 400);
        }

        $startDate = Carbon::createFromFormat("d-m-Y", request()->start_date)->subDay(1);
        $endDate = Carbon::createFromFormat("d-m-Y", request()->end_date);

        $custom_sale_records = Voucher::whereBetween("created_at", [$startDate, $endDate])->latest("id")->paginate(10)->withQueryString();

        // return $custom_sale_records;
        $total_cash = $custom_sale_records->sum("total");
        $total_tax = $custom_sale_records->sum("tax");
        $total = $custom_sale_records->sum("net_total");
        $total_vouchers = $custom_sale_records->sum("total_vouchers");

        return VoucherResource::collection($custom_sale_records)->additional(["total" => [
            "total_voucher" => $total_vouchers,
            "total_cash" => $total_cash,
            "total_tax" => round($total_tax,2),
            "total" => round($total, 2)
        ]]);
    }

    public function year(){
        return collect(DailySaleOverview::groupBy("year")->get("year"))->pluck("year");

    }
}
