<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MonthlySaleOverviewResource extends JsonResource
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
            "vouchers" => $this->total_vouchers,
            "date" => $this->created_at->format('d M Y'),
            "cash" => $this->total_cash,
            "tax" => $this->total_tax,
            "total" => $this->total
        ];
        return parent::toArray($request);
    }
}
