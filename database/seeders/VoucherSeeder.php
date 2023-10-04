<?php

namespace Database\Seeders;

use App\Models\Voucher;
use App\Models\VoucherRecord;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 50; $i++) {
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
