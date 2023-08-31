<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomSaleOverviewResource;
use App\Http\Resources\DailySaleOverviewResource;
use App\Http\Resources\MonthlySaleOverviewResource;
use App\Http\Resources\MonthlyTotalSaleOverviewResource;
use App\Http\Resources\TodaySaleOverviewResource;
use App\Http\Resources\TodayTotalSaleOverviewResource;
use App\Http\Resources\YearlySaleOverviewResource;
use App\Models\DailySaleOverview;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class DailySaleOverviewController extends Controller
{
    public function saleClose(Request $request)
    {
        // return $request->close;
        $totalCash = Voucher::whereDate('created_at', '=', Carbon::today()->toDateString())->sum("total");
            $totalTax = Voucher::whereDate('created_at', '=', Carbon::today()->toDateString())->sum("tax");
            $total = Voucher::whereDate('created_at', '=', Carbon::today()->toDateString())->sum("net_total");
            $totalVouchers = Voucher::whereDate('created_at', '=', Carbon::today()->toDateString())->count("id");
            $day =Carbon::today()->format("d");
            $month =Carbon::today()->format("m");
            $year =Carbon::today()->format("Y");

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

    public function daily(Request $request)
    {
        $daily_sale_records = Voucher::whereDate('created_at', '=', $request->date)->paginate(5);
        // return $daily_sale_records;

        $date = $request->date;
        $day = (new Carbon($date))->format("d");
        $dailyReport = DailySaleOverview::where('day', '=', $day)->first();
        // return $dailyReport;
        $dailyReport->daily_sale_records  = $daily_sale_records;

        // return $dailyReport;
        return new TodayTotalSaleOverviewResource($dailyReport);



    }

    public function monthly(Request $request)
    {

        // return $request->month;
        $monthly_sale_records = DailySaleOverview::where('month', $request->month)
        ->whereYear('created_at', date('Y'))
        ->get();

        return new MonthlyTotalSaleOverviewResource($monthly_sale_records);

    }

    public function yearly(Request $request)
    {

        $yearly_sale_records = DailySaleOverview::where('year', $request->year)
        ->get();

        return YearlySaleOverviewResource::collection($yearly_sale_records);

    }

    public function customSaleRecords(Request $request)
    {

        $custom_sale_records = Voucher::whereBetween('created_at',[$request->start_date,$request->end_date])->get();
        return TodaySaleOverviewResource::collection($custom_sale_records);

    }
}
