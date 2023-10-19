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
        $endDate = Carbon::now();
        $startDate = Carbon::create(2022, 7, 1);

        $period = CarbonPeriod::create($startDate, $endDate);
        $DailyTotalSale = [];
        foreach ($period as $day) {
            $date = $day;
            $dailyVoucher = Voucher::WhereDate('created_at', $date)->get();
            // $totalVoucher = $dailyVoucher->count('id');
            // $totalActualPrice = $dailyVoucher->sum('total');
            $voucherNumber = $dailyVoucher->voucher_number;
            $total = $dailyVoucher->sum('total');
            $taxTotal = $dailyVoucher->sum('tax');
            $netTotal = $dailyVoucher->sum('net_total');
            $userId = rand(1,2);
            $DailyTotalSale[] = [
                // "total_voucher" => $totalVoucher,
                // "total_actual_price" => $totalActualPrice,
                "voucher_number" => $voucherNumber,
                "total" => $total,
                "total_tax" => $taxTotal,
                "user_id" => $userId,
                "net_total" => $netTotal,
                "created_at" => $day,
                "updated_at" => $day
            ];
        }
        DailySaleOverview::insert($DailyTotalSale);
    }
}
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
    // }

