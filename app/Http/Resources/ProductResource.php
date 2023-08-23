<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sale_price' => $this->sale_price,
            'total_stock' => $this->total_stock,
            'brand_name' => $this->brand->name,
            'unit' => $this->unit,
        ];
        return parent::toArray($request);
    }
}
