<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stocks = [];
        $product_stock = [];
        for($i=1;$i<=20;$i++){
            $quantity = rand(1000,1200);
            $stocks[] = [
                "user_id" => 1,
                "product_id" => $i,
                "quantity" => $quantity,
                "more" => fake()->sentence(),
                "created_at" => now(),
                "updated_at" => now(),
            ];

            $currentProduct = Product::find($i);
            $currentProduct->total_stock +=$quantity;
            $currentProduct->update();
        }

        Stock::insert($stocks);

    }
}
