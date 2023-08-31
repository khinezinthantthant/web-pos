<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TodayTotalSaleOverviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "total_vouchers" => $this->total_vouchers,
            "total_cash" => $this->total_cash,
            "total_tax" => $this->total_tax,
            "total" => $this->total,
            "today_sale_overview" => TodaySaleOverviewResource::collection($this->daily_sale_records)
        ];
        // return parent::toArray($request);
    }


}
