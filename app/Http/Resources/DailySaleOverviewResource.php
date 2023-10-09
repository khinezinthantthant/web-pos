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
            "totalTax" => $totalTax,
            "totalCash" => $totalCash,
            "totalVouchers" => $totalVouchers,
            "voucher" => VoucherResource::collection($this),

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
