<?php

namespace App\Http\Resources;

use App\Models\DailySaleOverview;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class YearlySaleOverviewResource extends JsonResource
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
            "month" => $this->month,
            "year" => $this->year,
        ];
        return parent::toArray($request);
    }
}
