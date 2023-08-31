<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MonthlyTotalSaleOverviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "total_days" => $this->count("id"),
            "total_vouchers" => $this->sum("total_vouchers"),
            "total_cash" => $this->sum("total_cash"),
            "total_tax" => $this->sum("total_tax"),
            "total" => $this->sum("total"),
            "monthly_sale_overview" => MonthlySaleOverviewResource::collection($this)
        ];
        return parent::toArray($request);
    }
}
