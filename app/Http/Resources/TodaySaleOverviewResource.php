<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TodaySaleOverviewResource extends JsonResource
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
            "voucher" => $this->voucher_number,
            "time" => (new Carbon($this->created_at))->format('h:i A'),
            "item_count" => $this->voucher_records->count(),
            "cash" => $this->total,
            "tax" => $this->tax,
            "total" => $this->net_total,

            
        ];

        return parent::toArray($request);
    }
}
