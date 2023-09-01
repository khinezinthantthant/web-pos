<?php

namespace Database\Seeders;

use App\Models\DailySaleOverview;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DailySaleOverviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carbon = (new Carbon())->subMonths(3);
        while(!$carbon->isCurrentDay()){
            $daily_sale_overview = DailySaleOverview::create([
                "total_vouchers" => Voucher::whereDate("created_at",$carbon->toDate())->count("id"),
                "total_cash" => Voucher::whereDate("created_at",$carbon->toDate())->sum("total"),
                "total_tax" => Voucher::whereDate("created_at",$carbon->toDate())->sum("tax"),
                "total" => Voucher::whereDate("created_at",$carbon->toDate())->sum("net_total"),
                "day" => Carbon::today()->format("d"),
                "month" => Carbon::today()->format("m"),
                "year" => Carbon::today()->format("Y")
            ]);
        }
    }
}
