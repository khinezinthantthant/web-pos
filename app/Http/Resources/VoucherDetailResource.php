<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherDetailResource extends JsonResource
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
            "records" => VoucherRecordResource::collection($this->voucher_records),
            "total" => $this->total,
            "tax" => $this->tax,
            "net_total" => $this->net_total,
            // "actual_price" => $this->total_actual_price
        ];

        // return parent::toArray($request);
    }
}
