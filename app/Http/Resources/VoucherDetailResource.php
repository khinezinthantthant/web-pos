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
            "customer_name" => $this->customer_name,
            "phone_number" => $this->phone_number,
            "total" => $this->total,
            "tax" => $this->tax,
            "net_total" => $this->net_total,
        ];

        return parent::toArray($request);
    }
}
