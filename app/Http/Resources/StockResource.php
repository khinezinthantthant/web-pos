<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
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
            "product_name" => $this->product->name,
            "sale_price" => $this->product->sale_price,
            "brand_name" => $this->product->brand->name,
            "unit" => $this->product->unit,
            "user_name" => $this->user->name,
            "more" => $this->more,
            "total_stock" => $this->quantity,
            "created_at" => $this->created_at->format("d m Y"),
            "updated_at" => $this->updated_at->format("d m Y"),
        ];

        return parent::toArray($request);
    }
}
