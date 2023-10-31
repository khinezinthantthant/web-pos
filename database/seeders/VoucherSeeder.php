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
        // $endDate = Carbon::now();
        // $startDate = Carbon::create(2022, 7, 1);
        // $period = CarbonPeriod::create($startDate, $endDate);
        // foreach($period as $date) {
        //     $voucher = Voucher::factory()->create();
        //     VoucherRecord::factory(rand(1, 5))->create([
        //         'voucher_id' => $voucher->id
        //     ]);
        //     $total_cost = $voucher->voucher_records()->sum('cost');
        //     $voucher->net_total = $total_cost;
        //     $voucher->save();
        // }
        // $endDate = Carbon::now();
        // $startDate = Carbon::create(2022, 7, 1);
        // $period = CarbonPeriod::create($startDate, $endDate);
        // $voucher_id = 1;
        // /* This for loop (i) is for dates. */
        // foreach ($period as $date) {

        //     $vouchers = [];
        //     for ($r = 1; $r <= 2; $r++) {

        //         $prodIds = [];
        //         // $itemQuan = random_int);
        //         for ($i = 1; $i <= 2; $i++) {
        //             array_push($prodIds, random_int(1, 20));
        //         }

        //         $prods = Product::whereIn("id", $prodIds)->get();
        //         $total = 0;

        //         $records = [];

        //         foreach ($prods as $prod) {
        //             /* product quantity */
        //             $q = random_int(1,10);
        //             $cost = $prod->sale_price * $q;
        //             $total += $cost;

        //             $records[] = [
        //                 "voucher_id" => $voucher_id,
        //                 "product_id" => $prod->id,
        //                 "quantity" => $q,
        //                 "price" => $prod->sale_price,
        //                 "cost" => $cost,
        //                 "created_at" => $date,
        //                 "updated_at" => $date,
        //             ];

        //             $prod->total_stock -= $q;
        //             $prod->update();
        //         }

        //         VoucherRecord::insert($records);

        //         $tax = $total * 0.05;
        //         $netTotal = $total + $tax;

        //         $vouchers[] = [
        //             'customer_name' => fake()->name(),
        //             'phone_number' => fake()->phoneNumber(),
        //             'voucher_number' => rand(10000,99999),
        //             'total' => $total,
        //             'tax' => $tax,
        //             'net_total' => $netTotal,
        //             'user_id' => 1,
        //             "created_at" => $date,
        //             "updated_at" => $date,
        //         ];
        //         $voucher_id++;
        //     }
        //     Voucher::insert($vouchers);
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
                $totalActualPrice = 0;
                $total = 0;

                $records = [];
                foreach ($ids as $itemId) {
                    $quantity = random_int(1, 5);
                    $currentProduct = $products->find($itemId);
                    $totalActualPrice +=$quantity * $currentProduct->actual_price;
                    $total += $quantity * $currentProduct->sale_price;

                    $records[] = [
                        "voucher_id" => $id,
                        "product_id" => $itemId,
                        // "actual_price" => $currentProduct->actual_price,
                        "price" => $currentProduct->sale_price,
                        "quantity" => $quantity,
                        "cost" => $quantity * $currentProduct->sale_price,
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
                    'customer_name' => fake()->name(),
                    'phone_number' => fake()->phoneNumber(),
                    'voucher_number' => rand(1000,9999),
                    // "voucher_number" => $id,
                    // "total_actual_price" => $totalActualPrice,
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
