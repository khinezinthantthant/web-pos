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
        // for ($i = 0; $i < 50; $i++) {
        //     $voucher = Voucher::factory()->create();
        //     VoucherRecord::factory(rand(1, 5))->create([
        //         'voucher_id' => $voucher->id
        //     ]);
        //     $total_cost = $voucher->voucher_records()->sum('cost');
        //     $voucher->net_total = $total_cost;
        //     $voucher->save();
        // }



        $endDate = Carbon::now();
        $startDate = Carbon::create(2022, 7, 1);

        $period = CarbonPeriod::create($startDate, $endDate);
        $id = 1;
        foreach ($period as $index => $day) {
            $vouchers = [];
            $voucherCount = random_int(1, 5);
            for ($i = 1; $i <= $voucherCount; $i++) {
                $ids = [];
                $productId = random_int(1, 6);
                for ($y = 1; $y <= $productId; $y++) {
                    $ids[] = random_int(1, 20);
                }
                $products = Product::whereIn('id', $ids)->get();
                $total = 0;

                $records = [];
                foreach ($ids as $itemId) {
                    $quantity = random_int(1, 5);
                    $currentProduct = $products->find($itemId);
                    $total += $quantity * $currentProduct->sale_price;

                    $records[] = [
                        "voucher_id" => $id,
                        "product_id" => $itemId,
                        "price" => $currentProduct->actual_price,
                        "quantity" => $quantity,
                        "cost" => $quantity * $currentProduct->actual_price,
                        "created_at" => $day,
                        "updated_at" => $day
                    ];
                    Product::where("id", $itemId)->update([
                        "total_stock" => $currentProduct->total_stock - $quantity
                    ]);
                }
                VoucherRecord::insert($records); // use database

                $tax = $total * 0.05;
                $netTotal = $total + $tax;
                $vouchers[] = [
                    "voucher_number" => $id,
                    "total" => $total,
                    "tax" => $tax,
                    "net_total" => $netTotal,
                    "user_id" => 1,
                    "created_at" => $day,
                    "updated_at" => $day
                ];
                $id++;
            }
            Voucher::insert($vouchers);
        }
    }
}
