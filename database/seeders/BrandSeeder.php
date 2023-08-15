<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Brand::factory(10)->create();
        $brands = ["Coca Cola", "Milo", "Shark", "Apple", "Dell"];
        $arr = [];
        foreach($brands as $brand){
            $arr[] = [
                "name" => $brand,
                "company" => $brand,
                "information" => "contact our company",
                "photo" => "there is no photo",
                "user_id" => 1,
                "created_at" => now(),
                "updated_at" => now()
            ];
        }
        Brand::insert($arr);
    }
}
