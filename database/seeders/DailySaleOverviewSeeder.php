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
        foreach ($period as $date) {
            $total = random_int(200000, 666666);
            $rand = random_int(10000, 15000);
            $tax = random_int(3000, 6000);
            $netTotal = $total + $tax;
            $dailySales[] = [
                "total_vouchers" => $rand,
                "total_cash" => $total,
                "total_tax" => $tax,
                "total" => $netTotal,
                "created_at" => $date,
                "updated_at" => $date,
            ];
        }
DailySaleOverview::insert($dailySales);
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

