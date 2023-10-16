<?php

namespace App\Http\Resources;

use App\Models\DailySaleOverview;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class YearlySaleOverviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "month" => $this->created_at->format("M"),
            "year" => $this->created_at->format("Y"),
            "vouchers" => $this->total_vouchers,
            "cash" => $this->total_cash,
            "tax" => $this->total_tax,
            "total" => $this->total_cash + $this->total_tax,
        ];
        return parent::toArray($request);
    }
}
