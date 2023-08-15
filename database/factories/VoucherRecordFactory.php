<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VoucherRecord>
 */
class VoucherRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::find(rand(1, 10));
        $qty = rand(1, 10);
        $cost = $product->sale_price * $qty;
        return [
            // 'voucher_id' => ,
            'product_id' => $product->id,
            'quantity' => $qty,
            'cost' => $cost,
            'price' => $product->find(rand(1, 10))->sale_price

        ];
    }
}
