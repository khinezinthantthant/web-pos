<?php

namespace Database\Seeders;

use App\Models\SaleClose;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SaleCloseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SaleClose::factory()->create([
            'sale_close' => false
        ]);
    }
}
