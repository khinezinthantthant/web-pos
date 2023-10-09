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
            "monthly_sale_overview" => MonthlySaleOverviewResource::collection($this),

            'current_page' => $this->currentPage(),
            'first_page_url' => $this->url(1),
            'from' => $this->firstItem(),
            'last_page' => $this->lastPage(),
            'last_page_url' => $this->url($this->lastPage()),
            'next_page_url' => $this->nextPageUrl(),
            'path' => $this->path(),
            'per_page' => $this->perPage(),
            'prev_page_url' => $this->previousPageUrl(),
            'to' => $this->lastItem(),
            // 'total' => $this->total(),
        ];
        return parent::toArray($request);
    }
}
