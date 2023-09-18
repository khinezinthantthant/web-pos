<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class weeklySaleProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            "voucher_number" => $this->voucher_number,
            'total' => $this->total,
            'created_at' => $this->created_at->format('d/m/Y')

        ];
    }
}
