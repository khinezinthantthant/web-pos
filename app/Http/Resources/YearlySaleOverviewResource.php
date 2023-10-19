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
            // "id" => $this->id,
            // "month" => $this->month,
            // "year" => $this->year,
            // "total_vouchers" => $this->total_vouchers,
            // "total_cash" => $this->total_cash,
            // "total_tax" => $this->total_tax,
            // "total" => $this->total

            "month" => $this->created_at->format("M"),
            "year" => $this->created_at->format("Y"),
            "vouchers" => $this->total_vouchers,
            "total_cash" => $this->total_cash,
            "total_tax" => $this->total_tax,
            "total" => round($this->total_cash + $this->total_tax,2),
        ];
        return parent::toArray($request);
    }
}
