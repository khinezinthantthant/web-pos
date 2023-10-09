<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Voucher;
use App\Models\VoucherRecord;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Voucher>
 */
class VoucherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // $total = rand(2, 10) * 100;
        // $tax = $total * 0.05;
        // $net_total = $total + $tax;

        // $carbon = new Carbon();
        // $carbon->subMonth(rand(1,12));
        // $carbon->addDays(rand(1,30));

        // return [
        //     'customer_name' => fake()->name(),
        //     'phone_number' => fake()->phoneNumber(),
        //     'voucher_number' => fake()->regexify('[A-Z0-9]{8}'),
        //     'total' => $total,
        //     'tax' => $tax,
        //     'net_total' => $net_total,
        //     'user_id' => rand(1, 3),
        //     'created_at' => $carbon->getTimestamp(),
        //     'updated_at' => $carbon->getTimestamp()
        // ];


        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 6; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return [
            'phone' => fake()->phoneNumber(),
            "voucher_number" => $randomString,
            "user_id" => 1
        ];

    }
}
