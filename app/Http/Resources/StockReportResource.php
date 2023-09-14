<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->total_stock == 0){
            $stock_level = 'out of stock';
        }elseif($this->total_stock < 10){
            $stock_level = 'low stock';
        }else{
            $stock_level = 'instock';
        }

        return [
            "id" => $this->id,
            "name" => $this->name,
            "brand" => $this->brand->name,
            "sale_price" => $this->sale_price,
            "unit" => $this->unit,
            "total_stock" => $this->total_stock,
            "stock_level" => $stock_level
        ];
        // return parent::toArray($request);
    }
}
