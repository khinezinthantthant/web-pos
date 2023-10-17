<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "product_name" => $this->name,
            "sale_price" => $this->sale_price,
            "brand_name" => $this->brand->name,
            "unit" => $this->unit,
            // "user_name" => $this->user->name,
            // "more" => $this->more,
            "total_stock" => $this->total_stock,
            // "created_at" => $this->created_at->format("d m Y"),
            // "updated_at" => $this->updated_at->format("d m Y"),
        ];
    }
}
