<?php

namespace Database\Seeders;

use App\Models\DailySaleOverview;
use App\Models\MonthlySaleOverview;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MonthlySaleOverviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startOfMonth = Carbon::create(2022, 7, 1);
        $sales = [];

        while($startOfMonth->format("M Y") != Carbon::now()->format("M Y")) {
            $endOfMonth = $startOfMonth->copy()->endOfMonth();
            $dailyVoucher = DailySaleOverview::whereBetween('created_at', [$startOfMonth, $endOfMonth])->get();
            $totalVoucher = $dailyVoucher->sum('total_vouchers');

            $totalActualPrice = $dailyVoucher->sum('total_actual_price');
            $cashTotal = $dailyVoucher->sum('total_cash');
            $taxTotal = $dailyVoucher->sum('total_tax');
            $total = $dailyVoucher->sum('total');
            $sales[] = [
                "total_vouchers" => $totalVoucher,
                "total_actual_price" => $totalActualPrice,
                "total_cash"  => $cashTotal,
                "total_tax" => $taxTotal,
                "total" => $total,
                "created_at" => $endOfMonth,
                "updated_at" => $endOfMonth,
                "month" => $endOfMonth->format('m'),
                "year" => $endOfMonth->format('Y'),
            ];
            $startOfMonth->addMonth();
        }
        MonthlySaleOverview::insert($sales);
    }
}
