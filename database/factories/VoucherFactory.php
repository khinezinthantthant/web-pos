<?php

namespace Database\Factories;

use Carbon\Carbon;
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
        $total = rand(2, 10) * 100;
        $tax = $total * 0.05;
        $net_total = $total + $tax;
        $carbon = new Carbon();
        $carbon->subMonth(rand(1,3));
        $carbon->addDays(rand(1,30));

        return [
            'customer_name' => fake()->name(),
            'phone_number' => fake()->phoneNumber(),
            'voucher_number' => fake()->regexify('[A-Z0-9]{8}'),
            'total' => $total,
            'tax' => $tax,
            'net_total' => $net_total,
            'user_id' => rand(1, 3),
            'created_at' => $carbon->getTimestamp(),
            'updated_at' => $carbon->getTimestamp()
        ];
        // return [
        //     "customer_name"=> fake()->name(),
        //     "phone_number" => fake()->phoneNumber(),
        //     "voucher_number" => fake()->uuid(),
        //     "total" => rand(1000,100000),
        //     "tax" => rand(10,100),
        //     "net_total" => rand(1000,100000),
        //     "user_id" => rand(1,5)
        // ];
    }
}
