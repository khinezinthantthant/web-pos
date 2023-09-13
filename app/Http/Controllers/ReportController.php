<?php

namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


class ReportController extends Controller
{
    public function stockLevelCheck()
    {
        
    }
    public function getLowStockProducts()
    {
        $lowStockProducts = Product::where('total_stock', '<', 10)->get();
        return response()->json([
            'message' => 'Low stock products',
            'data' => $lowStockProducts
            
        ], 200);
    }
    public function instockProducts()
    {
        $instockProduct = Product::where('total_stock', '>=', 10)->get();
        return response()->json([
            'message' => 'In stock products',
            'data' => $instockProduct
        ], 200);
    }
    public function outOfStockProducts()
    {
        $outOfStockProducts = Product::where('total_stock', '=', 0)->get();
        
        // return $outOfStockProducts;
            return response()->json([
                'message' => 'out of stock products',
                'data' => $outOfStockProducts,
            ], 200);
        
    }

}
