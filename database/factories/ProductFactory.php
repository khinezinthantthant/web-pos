<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       
        $actual_price = rand(50,1000);
        $sale_price = $actual_price + 50;
        $unit = ["single","dozen"];

        return [
            "name" => fake()->name(),
            "brand_id"=>rand(1,10),
            "actual_price"=> $actual_price,
            "sale_price"=>$sale_price,
            "total_stock"=>0,
            "unit"=>array_rand($unit),
            "more_information"=>fake()->text(),
            "user_id"=> 1,
        ];
    }
}
