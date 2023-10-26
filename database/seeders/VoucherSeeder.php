<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Voucher;
use App\Models\VoucherRecord;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $endDate = Carbon::now();
        $startDate = Carbon::create(2022, 7, 1);
        $period = CarbonPeriod::create($startDate, $endDate);
        foreach($period as $date) {
            $voucher = Voucher::factory()->create();
            VoucherRecord::factory(rand(1, 5))->create([
                'voucher_id' => $voucher->id
            ]);
            $total_cost = $voucher->voucher_records()->sum('cost');
            $voucher->net_total = $total_cost;
            $voucher->save();
        }
    }
}
