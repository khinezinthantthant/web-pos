<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomSaleOverviewResource;
use App\Http\Resources\DailySaleOverviewResource;
use App\Http\Resources\MonthlySaleOverviewResource;
use App\Http\Resources\MonthlyTotalSaleOverviewResource;
use App\Http\Resources\TodaySaleOverviewResource;
use App\Http\Resources\TodayTotalSaleOverviewResource;
use App\Http\Resources\YearlySaleOverviewResource;
use App\Http\Resources\YearlyTotalSaleOverviewResource;
use App\Models\DailySaleOverview;
use App\Models\MonthlySaleOverview;
use App\Models\SaleClose;
use App\Models\Voucher;
use App\Models\YearlySaleOverview;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
   
class DailySaleOverviewController extends Controller
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
        $year = (new Carbon($request->date))->format("Y");
        $month = (new Carbon($request->date))->format("m");

        $daily_sale_records = Voucher::whereDate('created_at', '=', $request->date)
            // ->whereYear("created_at",$year)
            ->paginate(5);
        // return $daily_sale_records;
        $date = $request ->date;
        // return $date;
        $day = (new Carbon($date))->format("d");
        // return $day;

        $dailyReport = DailySaleOverview::where('day', '=', $day)
            ->where("month", $month)
            ->where("year", $year)
            ->first();
        return $dailyReport;
        $dailyReport->daily_sale_records  = $daily_sale_records;

        // return $dailyReport;
        return new TodayTotalSaleOverviewResource($dailyReport);
    }

    public function monthly(Request $request)
    {
        // $monthly_sale_records = DailySaleOverview::where('month', $request->month)
        // ->whereYear('created_at', date('Y'))
        // ->get();

        $monthly_sale_records = DailySaleOverview::where('month', $request->month)
            ->where('year', $request->year)
            ->get();

        return new MonthlyTotalSaleOverviewResource($monthly_sale_records);
    }

    public function yearly(Request $request)
    {
        $months = DailySaleOverview::where('year', $request->year)
            ->get()->groupBy("month");

        if (MonthlySaleOverview::count() <= 0) {
            foreach ($months as $key => $month) {
                $total_vouchers = $month->sum("total_vouchers");
                $total_cash = $month->sum("total_cash");
                $total_tax = $month->sum("total_tax");
                $total = $month->sum("total");
                $month = $key;
                $year = $request->year;

                $yearly_sale_overviews = MonthlySaleOverview::create([
                    "id" => rand(1, 12),
                    "total_vouchers" => $total_vouchers,
                    "total_cash" => $total_cash,
                    "total_tax" => $total_tax,
                    "total" => $total,
                    "month" => $month,
                    "year" => $year
                ]);

                // return new YearlyTotalSaleOverviewResource($yearly_sale_overviews);
            }
        } else {
            MonthlySaleOverview::truncate();
        }

        // return $yearly_sale_overviews;
        // return new YearlyTotalSaleOverviewResource($yearly_sale_overviews);

        return response()->json([
            "yearly_total_sale_overview" => new YearlyTotalSaleOverviewResource($yearly_sale_overviews),
            "yearly_sale_overviews" => MonthlySaleOverview::all()
        ]);
    }

    public function customSaleRecords(Request $request)
    {

        $startDate = $request->start_date . " 00::00::00";
        // return $startDate;
        $endDate = $request->end_date . " 23::59::59";
        $custom_sale_records = Voucher::whereBetween('created_at', [$startDate, $endDate])->get();

        return TodaySaleOverviewResource::collection($custom_sale_records);
    }

    public function year(){
        return collect(DailySaleOverview::groupBy("year")->get("year"))->pluck("year");

    }
}
