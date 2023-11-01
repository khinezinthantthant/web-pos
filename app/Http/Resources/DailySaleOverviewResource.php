<?php

namespace App\Http\Resources;

use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DailySaleOverviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $totalCash = Voucher::whereDate('created_at', '=', Carbon::today()->toDateString())->sum("total");
        $totalTax = Voucher::whereDate('created_at', '=', Carbon::today()->toDateString())->sum("tax");
        $total = Voucher::whereDate('created_at', '=', Carbon::today()->toDateString())->sum("net_total");
        $totalVouchers = Voucher::whereDate('created_at', '=', Carbon::today()->toDateString())->count("id");

        return [
            "total" => $total,
            "totalTax" => round($totalTax,2),
            "totalCash" => $totalCash,
            "totalVouchers" => $totalVouchers,
            "voucher" => VoucherResource::collection($this)

        ];
        return parent::toArray($request);
    }
}
