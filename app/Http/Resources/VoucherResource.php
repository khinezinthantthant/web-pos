<?php

namespace App\Http\Resources;

use App\Models\Voucher;
use Carbon\Carbon;
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
            "voucher_number" => $this->voucher_number,
            "customer_name" => $this->customer_name,
            "sale_person" => $this->user->name,
            "phone_number" => $this->phone_number,
            "cash" => $this->total,
            "tax" => $this->tax,
            "total" => $this->net_total,
            "item_count" => $this->voucher_records->count(),
            "time" => (new Carbon($this->created_at))->format('h:i A'),

        ];


        return parent::toArray($request);
    }
}
