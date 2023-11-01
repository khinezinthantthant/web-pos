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
        for ($i = 1; $startOfMonth->format("M Y") != Carbon::now()->format("M Y"); $i++) {
            $endOfMonth = $startOfMonth->copy()->endOfMonth();
            $dailyVoucher = DailySaleOverview::whereBetween('created_at', [$startOfMonth, $endOfMonth])->get();
            $totalVoucher = $dailyVoucher->sum('total_vouchers');
            $totalActualPrice = $dailyVoucher->sum('total_actual_price');
            $cashTotal = $dailyVoucher->sum('total_cash');
            $taxTotal = $dailyVoucher->sum('total_tax');
            $total = $cashTotal + $taxTotal;
            $sales[] = [
                "total_vouchers" => $totalVoucher,
                "total_actual_price" => $totalActualPrice,
                "total_cash"  => $cashTotal,
                "total_tax" => $taxTotal,
                "total" => $total,
                "created_at" => $endOfMonth,
                "updated_at" => $endOfMonth,
                // "month" => $endOfMonth->format('m'),
                // "year" => $endOfMonth->format('Y'),
            ];
            $startOfMonth->addMonth();
        }
        MonthlySaleOverview::insert($sales);
        // $vouchers = [];

        // $years = ["2022", "2023"];

        // foreach ($years as $year) {

        //     for ($month = 1; $month <= 12; $month++) {
        //         $quan = random_int(300, 350);
        //         $total = rand(3000000, 4000000);
        //         $tax = rand(8000, 10000);
        //         $netTotal = rand(3008000, 4010000);
        //         $vouchers[] = [
        //             "total_vouchers" => $quan,
        //             "total_cash" => $total,
        //             "total_tax" => $tax,
        //             "total" => $netTotal,
        //             "created_at" => Carbon::createFromDate($year, $month),
        //             "updated_at" => Carbon::createFromDate($year, $month),
        //         ];
        //     }
        // }

        // MonthlySaleOverview::insert($vouchers);
    
    }
}
