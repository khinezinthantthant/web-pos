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
        for ($i = 1; $i <= 20; $i++) {
            // $currentQuantity = rand(1, 100);

            $currentProduct = Product::find($i);
            // $currentProduct->total_stock = $currentQuantity;
            $currentProduct->total_stock = 1000;
            $currentProduct->save();

            $stocks[] = [
                "user_id" => 1,
                "product_id" => $i,
                // "quantity" => $currentQuantity,
                "quantity" => 1000,
                "created_at" => now(),
                "updated_at" => now(), 
            ];
        }

        Stock::insert($stocks);

    }
}
