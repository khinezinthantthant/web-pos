<?php

namespace Database\Seeders;

use App\Models\DailySaleOverview;
use App\Models\Voucher;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DailySaleOverviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $startDate = (new Carbon())->subYear(2)->subMonths(3);
        $endDate = Carbon::now();
        $startDate = Carbon::create(2022, 7, 1);
        $period = CarbonPeriod::create($startDate, $endDate);

        $dailySaleOverview = [];

        foreach($period as $day){

            $date = $day;
            $dailyVoucher = Voucher::whereDate("created_at",$date)->get();

            $totalVoucher = $dailyVoucher->count("id");
            $totalActualPrice = $dailyVoucher->sum('total_actual_price');
            $total = $dailyVoucher->sum("total");
            $totalTax = $dailyVoucher->sum("tax");
            $netTotal = $dailyVoucher->sum("net_total");

            $d = Carbon::parse($date)->format('d');
            $m = Carbon::parse($date)->format('m');
            $y = Carbon::parse($date)->format('Y');

            $dailySaleOverview [] = [
                "total_cash" => $netTotal,
                "total_tax" => $totalTax,
                "total_actual_price" => $totalActualPrice,
                "total" => $total,
                "total_vouchers" => $totalVoucher,
                "day" => $d,
                "month" => $m,
                "year" => $y,
                "created_at" => $date,
                "updated_at" => $date
            ];

        }

        DailySaleOverview::insert($dailySaleOverview);

        // $carbon = (new Carbon())->subMonths(3);
        // while(!$carbon->isCurrentDay()) {
        //     $dailySaleOverview = DailySaleOverview::create([
        //             "total_vouchers" => Voucher::whereDate("created_at", $carbon->toDate())->count('id'),
        //             "total_cash" => Voucher::whereDate("created_at", $carbon->toDate())->sum('total'),
        //             "total_tax" => Voucher::whereDate("created_at", $carbon->toDate())->sum('tax'),
        //             "total" => Voucher::whereDate("created_at", $carbon->toDate())->sum('net_total'),
        //             "day" => $carbon->format('d'),
        //             "month" => $carbon->format('m'),
        //             "year" => $carbon->format('Y'),
        //     ]);
        //     $carbon->addDay();
        // }

    }
}
