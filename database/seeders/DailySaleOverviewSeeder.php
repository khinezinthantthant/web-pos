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
            $totalVoucher = $dailyVoucher->count('id');
            $totalActualPrice = $dailyVoucher->sum('total_actual_price');
            $totalCash = $dailyVoucher->sum('total');
            $taxTotal = $dailyVoucher->sum('tax');
            $netTotal = $totalCash + $taxTotal;
            $DailyTotalSale[] = [
                "total_vouchers" => $totalVoucher,
                "total_actual_price" => $totalActualPrice,
                "total_cash" => $totalCash,
                "total_tax" => $taxTotal,
                "total" => $netTotal,
                "created_at" => $day,
                "updated_at" => $day
            ];
        }
        DailySaleOverview::insert($DailyTotalSale);

    }
}
