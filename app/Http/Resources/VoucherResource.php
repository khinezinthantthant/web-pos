<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoucherResource extends JsonResource
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
            "customer_name" => $this->customer_name,
            "phone_number" => $this->phone_number,
            "net_total" => $this->net_total,
            "count" => $this->voucherRecords->count()
        ];

        return parent::toArray($request);
    }
}
