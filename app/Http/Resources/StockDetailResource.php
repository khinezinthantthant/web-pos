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
        // return parent::toArray($request);
        return [
            "id" => $this->id,
            "name" => $this->name,
            "brand" => $this->brand->name,
            "unit" => $this->unit,
            "sale_price" => $this->sale_price,
            "total_stock" => $this->total_stock,
        ];
    }
}
