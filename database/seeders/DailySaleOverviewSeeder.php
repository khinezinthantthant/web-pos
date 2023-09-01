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
        while(!$carbon->isCurrentDay()) {
            $dailySaleOverview = DailySaleOverview::create([
                    "total_vouchers" => Voucher::whereDate("created_at", $carbon->toDate())->count('id'),
                    "total_cash" => Voucher::whereDate("created_at", $carbon->toDate())->sum('total'),
                    "total_tax" => Voucher::whereDate("created_at", $carbon->toDate())->sum('tax'),
                    "total" => Voucher::whereDate("created_at", $carbon->toDate())->sum('net_total'),
                    "day" => $carbon->format('d'),
                    "month" => $carbon->format('m'),
                    "year" => $carbon->format('Y'),
            ]);
            $carbon->addDay();
        }
    }
}
