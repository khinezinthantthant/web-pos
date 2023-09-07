<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class YearlyTotalSaleOverviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "total_vouchers" => $this->sum("total_vouchers"),
            "total_cash" => $this->sum("total_cash"),
            "total_tax" => $this->sum("total_tax"),
            "total" => $this->sum("total"),
            "total_month" => $this->count("id"),
            // "yearly_sale_overview" =>  YearlySaleOverviewResource::collection($this)
        ];

        return parent::toArray($request);
    }
}
